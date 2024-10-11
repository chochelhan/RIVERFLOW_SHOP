<?php

namespace App\Services\Admin\Core\RelationOstatus;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderClaimRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInventoryRepository;

use App\Events\MailEvent;
use App\Events\SmsEvent;

/** 문자 ,메일 , 재고처리  ****/

class RelationOstatusService  {

    protected $orderRepository;
    protected $claimRepository;
    protected $productRepository;
    protected $productInventoryRepository;

    public function __construct(CustomizeOrderRepository $orderRepository,
                                CustomizeOrderClaimRepository $claimRepository,
                                CustomizeProductRepository $productRepository,
                                CustomizeProductInventoryRepository $productInventoryRepository) {

        $this->orderRepository = $orderRepository;
        $this->claimRepository = $claimRepository;
        $this->productRepository = $productRepository;
        $this->productInventoryRepository = $productInventoryRepository;

    }
    public function relationOrderService(array $params) {

        // 문자 및 메일 처리
        switch($params['nowstatus']) {
            case 'income':
            case 'DI':
                $type = ($params['nowstatus'] == 'DI')?'delivery':$params['nowstatus'];
                $oids = [];
                foreach($params['oldstatus'] as $opId=>$odata) {
                    $oids[$odata['oid']] = $odata;
                }
                foreach($oids as $odata) {
                    $oid = $odata['oid'];
                    if($odata['ostatus']=='DR') {
                        $orderInfo = $this->orderRepository->getOrderInfo($oid);
                        $productList = $this->orderRepository->getOrderProductList($oid);
                        if($orderInfo) {
                            $this->serviceRun($type,$orderInfo,$productList);
                        }
                    }
                }

                break;
            case 'CC':
            case 'RC':
            case 'EC':
                $type = $params['nowstatus'];
                $oids = [];
                foreach($params['oldstatus'] as $opId=>$odata) {
                     $oids[$odata['oid']]['data'] = $odata;
                     $oids[$odata['oid']]['opId'][] = $opId;

                }
                foreach($oids as $oid=>$data) {
                    $orderInfo = $this->orderRepository->getOrderInfo($oid);
                    $productList = $this->orderRepository->getOrderProductListByIds($data['opId']);
                    if($orderInfo) {
                        $orderInfo->recoverData = $data['data'];
                        $this->serviceRun($type,$orderInfo,$productList);
                    }
                }
                break;
            default:
                return;
                break;
        }
    }
    private function serviceRun($type,$orderInfo,$productList) {



        $eventParams['type'] = $type;
        $eventParams['orderDate'] = substr($orderInfo->created_at,1,10);
        $eventParams['userName']  = $orderInfo->oname;

        $pname = '';
        $orderAmt = 0;
        foreach($productList as $product) {
            if(!$pname) {
                $pname = $product['pname'];
            } else {
                $orderAmt = $orderAmt + $product['oamt'];
            }
        }
        if($orderAmt>0) {
            $eventParams['orderInfo']  = $pname.' 외 '.$orderAmt.'개';
        } else {
            $eventParams['orderInfo']  = $pname;
        }
        $eventParams['cancleProduct'] = $eventParams['orderInfo']; //취소상품정보
        $eventParams['returnProduct'] = $eventParams['orderInfo']; //반품상품정보
        $eventParams['exchangeProduct'] = $eventParams['orderInfo']; //'교환상품정보'

        if($type=='CC' || $type=='RC') {
            $refundPriceInfo = '';
            if(!empty($orderInfo->recoverData->recoverPrice)) {
                $refundPriceInfo = '환불금액:'.number_format($orderInfo->recoverData->recoverPrice)."원 ";
            }
            if(!empty($orderInfo->recoverData->recoverPoint)) {
                $refundPriceInfo.= '환불적립금:'.number_format($orderInfo->recoverData->recoverPoint)."원 ";
            }
            if(!empty($orderInfo->recoverData->recoverPoint)) {
                $refundPriceInfo.= '환불쿠폰 있음 ';
            }
            if($refundPriceInfo) $eventParams['refundPrice'] = $refundPriceInfo;
        }

        $payMethods = config('order.paymethods');
        $msgpaymentInfo = '결제방법:'.$payMethods[$orderInfo->paymethod];
        if($orderInfo->deliveryId) {
            $msgpaymentInfo.= ' 배송비:'.number_format($orderInfo->deliveryPrice).'원';
        }
        $totalPaymentPrice = number_format($orderInfo->price);
        $msgpaymentInfo.= ' 결제금액:'.$totalPaymentPrice.'원';
        $eventParams['paymentInfo'] = $msgpaymentInfo;


        if(!empty($orderInfo->oemail)) {
            $eventParams['to'] = $orderInfo->oemail;
            \Event::dispatch(new \App\Events\MailEvent($eventParams));
        }
        if(!empty($orderInfo->opcs)) {
            $eventParams['to'] = $orderInfo->opcs;
            \Event::dispatch(new \App\Events\SmsEvent($eventParams));
        }

        /// 재고 정보 변경
        switch($type) {
            case 'delivery': //
            case 'CC':
            case 'RC':
            case 'EC':
                $this->inventoryUpdate($type,$productList);
            break;
        }

    }

    /// 상품 재고  수정
    private function inventoryUpdate($type,$list) {
        $productList = [];
        foreach($list as $data) {
            if(!empty($data->optionSingleInfos)) {
                $optionSingleInfos = json_decode($data->optionSingleInfos);
                foreach($optionSingleInfos as $option) {
                    if($option->required == 'N') {
                        $productList[] = ['pid'=>$data->pid,'opt_id'=>$option->opt_id,'amt'=>$option->camt,'invAmt'=>$option->invAmt,'ostatus'=>$data->ostatus];
                    }
                }
            }
            $optId = (!empty($data->opt_id))?$data->opt_id:'';
            $productList[] = ['pid'=>$data->pid,'opt_id'=>$optId,'amt'=>$data->oamt,'invAmt'=>$data->tempInvAmt,'ostatus'=>$data->ostatus];
        }
        foreach($productList as $product) {
            $this->updateInventoryAction($type,$product);
        }
    }
    private function updateInventoryAction($type,$data) {
        if(!empty($data['opt_id'])) {
            //$params['amt'] = $data['invAmt'] - $data['amt']; // 재고수량
            //$this->productRepository->updateProductOption($data['opt_id'],$params);
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByOid($data['opt_id']);
        } else if(!empty($data['pid'])) {
            //$params['amt'] = $data['invAmt'] - $data['amt']; // 재고수량
            //$this->productRepository->updateProduct($data['pid'],$params);
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByPid($data['pid']);
        } else {
            return;
        }
        if(!$inventoryInfo)return;

        $changeAmt = $data['amt'];

        $inventoryParams = [];
        switch($type) {
            case 'delivery': // 상품발송 (배송중)
                if($data['ostatus'] == 'DR') { // 이전 단계가 배송준비중일 경우
                    $able_amt = $inventoryInfo->able_amt;  // 판매가능한 재고
                    $disable_amt = $inventoryInfo->disable_amt - $changeAmt; // 판매 불가능한 재고
                    $total_amt = $inventoryInfo->total_amt - $changeAmt;
                    $sale_amt = $changeAmt;
                } else {
                    return;
                }
            break;
            case 'CC': // 취소완료
                $able_amt = $inventoryInfo->able_amt + $changeAmt;  // 판매가능한 재고
                $disable_amt = $inventoryInfo->disable_amt - $changeAmt; // 판매 불가능한 재고
                $total_amt = $inventoryInfo->total_amt;
                $sale_amt = $inventoryInfo->sale_amt;
            break;
            case 'RC': // 반품완료
                $able_amt = $inventoryInfo->able_amt + $changeAmt;  // 판매가능한 재고
                $disable_amt = $inventoryInfo->disable_amt; // 판매 불가능한 재고
                $total_amt = $inventoryInfo->total_amt + $changeAmt;
                $sale_amt = $inventoryInfo->sale_amt - $changeAmt;
            break;
            case 'EC': // 교환완료
                $able_amt = $inventoryInfo->able_amt - $changeAmt;  // 판매가능한 재고
                $disable_amt = $inventoryInfo->disable_amt; // 판매 불가능한 재고
                $total_amt = $inventoryInfo->total_amt;
                $sale_amt = $inventoryInfo->sale_amt;
            break;

        }
        if($able_amt < 0)$able_amt = 0;
        if($disable_amt < 0) $disable_amt = 0;
        if($total_amt <0) $total_amt = 0;
        if($sale_amt < 0) $sale_amt = 0;

        $inventoryParams['able_amt'] = $able_amt;
        $inventoryParams['disable_amt'] = $disable_amt;
        $inventoryParams['total_amt'] = $total_amt;
        $inventoryParams['sale_amt'] = $sale_amt;

        $this->productInventoryRepository->updateInventoryProduct($inventoryParams,$inventoryInfo->id);
        // 이벤트 정보 (재고히스토리)
        $eventParams['ivt_id'] = $inventoryInfo->id;
        $eventParams['type'] = $type;
        $eventParams['content'] = ['addAmt'=>$changeAmt,'able_amt'=>$able_amt,'disable_amt'=>$disable_amt,'total_amt'=>$total_amt,'sale_amt'=>$sale_amt];
        \Event::dispatch(new \App\Events\InventoryHistoryEvent($eventParams));

    }
}