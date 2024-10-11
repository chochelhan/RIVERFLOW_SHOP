<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiMemberLevelRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiPointRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiCouponRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiOrderRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiOrderClaimRepository;
use App\Repositories\Repository\Base\BaseSettingDeliveryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductInventoryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;

use App\Services\Api\Core\Common\CommonOrderRecoverService;


use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;


class CoreApiMypageService extends CoreApiAuthHeader {

    protected $orderRepository;
    protected $memberRepository;
    protected $memberLevelRepository;
    protected $pointRepository;
    protected $orderClaimRepository;
    protected $recoverService;
    protected $couponRepository;
    protected $deliveryRepository;
    protected $productInventoryRepository;
    protected $productRepository;


    protected $config;
    protected $pointConfig;

    public function __construct(Request $request,CustomizeApiMemberRepository $memberRepository,
                                CustomizeApiMemberLevelRepository $memberLevelRepository,
                                CustomizeApiPointRepository $pointRepository,
                                CustomizeApiCouponRepository $couponRepository,
                                CustomizeApiOrderRepository $orderRepository,
                                BaseSettingDeliveryRepository $deliveryRepository,
                                CustomizeApiOrderClaimRepository $orderClaimRepository,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiProductInventoryRepository $productInventoryRepository,
                                CommonOrderRecoverService $recoverService) {


        parent::__construct($request);

        $this->orderRepository = $orderRepository;
        $this->memberRepository = $memberRepository;
        $this->memberLevelRepository = $memberLevelRepository;
        $this->pointRepository = $pointRepository;
        $this->couponRepository = $couponRepository;
        $this->deliveryRepository = $deliveryRepository;
        $this->productInventoryRepository = $productInventoryRepository;
        $this->productRepository = $productRepository;

        $this->orderClaimRepository = $orderClaimRepository;
        $this->recoverService = $recoverService;



        $this->config = ($this->siteInfos['member'])?$this->siteInfos['member']:'';
        $this->pointConfig = ($this->siteInfos['points'])?$this->siteInfos['points']:'';

    }

    // 메인
    public function getMyMain(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $params = $request->all();

        /// 보유 적립금
        $data['point'] = $this->isLoginInfo->point;
        $data['pointName'] = ($this->pointConfig)?$this->pointConfig->pointName:'';
        $data['pointUnit'] = ($this->pointConfig)?$this->pointConfig->pointUnit:'';


        $data['ostatus'] =  config('order.status');
        /// 사용가능한 보유 쿠폰수
        $data['coupon'] = $this->couponRepository->getUserCouponCount($this->isLoginInfo->id);

        $params['limit'] = 10;
        $dateParams = ['stdate'=>'','endate'=>''];
        $data['statusData'] =  $this->orderRepository->getMyOrderStatus($this->isLoginInfo->id,$dateParams);
        $orderData =  $this->orderRepository->getMyOrderList($this->isLoginInfo->id,$params);
        $data['orderList'] = $orderData;

        $data['ostatus'] = config('order.status');
        $data['cancleStatus'] = config('order.cancleStatus');
        $data['returnStatus'] = config('order.returnStatus');
        $data['exchangeStatus'] = config('order.exchangeStatus');
        $data['refundStatus'] = config('order.refundStatus');

        $levelInfo = $this->memberLevelRepository->getLevelInfo($this->isLoginInfo->lvid);
        $data['levelName'] = ($levelInfo && $levelInfo->gname)?$levelInfo->gname:'';
        $data['memberInfo'] = $this->isLoginInfo;

        if(isMobile()!='pc') {
            $data['wishCount'] = $this->productRepository->getMyWishCount($this->isLoginInfo->id);
        }
        return ['status'=>'success','data'=>$data];

    }

    public function getMemberData() {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $data = $this->isLoginInfo;
        $levelInfo = $this->memberLevelRepository->getLevelInfo($this->isLoginInfo->lvid);
        $data->gname = $levelInfo->gname;

        return ['status'=>'success','data'=>$data];

    }

    public function getMemberInfo() {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $data['memberInfo'] = $this->isLoginInfo;
        $data['memberSetting'] = ($this->siteInfos['member'])?$this->siteInfos['member']:'';

        return ['status'=>'success','data'=>$data];

    }

    // 주문내역
    public function getOrderList(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $params = $request->all();
        if(empty($params['limit']))$params['limit'] = 100;
        $data['statusData'] =  $this->orderRepository->getMyOrderStatus($this->isLoginInfo->id,$params);
        $data['orderList'] =  $this->orderRepository->getMyOrderList($this->isLoginInfo->id,$params);
        $data['ostatus'] = config('order.status');
        $data['cancleStatus'] = config('order.cancleStatus');
        $data['returnStatus'] = config('order.returnStatus');
        $data['exchangeStatus'] = config('order.exchangeStatus');
        $data['refundStatus'] = config('order.refundStatus');

        return ['status'=>'success','data'=>$data];

    }

    // 주문상세
    public function getOrderDatail(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $data['orderList'] =  $this->orderRepository->getMyOrderDetail($this->isLoginInfo->id,$request->input('id'));
        $data['ostatus'] = config('order.status');
        $data['paymethodNames'] = config('order.paymethods');
        $data['orderSetting'] = $this->siteInfos['order'];


        $data['cancleStatus'] = config('order.cancleStatus');
        $data['returnStatus'] = config('order.returnStatus');
        $data['exchangeStatus'] = config('order.exchangeStatus');
        $data['refundStatus'] = config('order.refundStatus');

        return ['status'=>'success','data'=>$data];

    }


    // 주문상품 목록
    public function getOrderProductList(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $data =  $this->orderRepository->getMyOrderProductList($this->isLoginInfo->id,$request->input('oid'));
        return ['status'=>'success','data'=>$data];

    }

    // 적립금 목록
    public function getPointList(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $data['ablePoint'] = $this->isLoginInfo->point;
        $data['pointName'] = ($this->pointConfig)?$this->pointConfig->pointName:'';
        $data['pointUnit'] = ($this->pointConfig)?$this->pointConfig->pointUnit:'';
        $data['pointList'] = $this->pointRepository->getMyPointList($this->isLoginInfo->id,$request->all());
        return ['status'=>'success','data'=>$data];

    }
    // 구매확정
    public function updateOrderComplete(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $orderList =  $this->orderRepository->getMyOrderDetail($this->isLoginInfo->id,$request->input('id'));
        $errorFlag = false;
        $ocIdsInfo = [];
        $ocIds = [];
        $orderInfo = '';
        foreach($orderList as $val) {
            if($val->ordStatus != 'DC') {
                $errorFlag = true;
            }
            $orderInfo = $val;
            $ocIdsInfo[$val->opId] = ['ostatus'=>'DC','oid'=>$val->id]; // 주문상품에 변경할 데이타를 저장한다
            $ocIds[] = $val->opId;
        }
        if($errorFlag || !$orderInfo) {
            $status = 'error';
        } else {
            $this->orderRepository->updateMyOrderStatus($ocIds,'OC');
            if($orderInfo->reservePoint) {
                $pointParams['pointMsg'] = $this->pointRepository->PCODES['order'];
                $pointParams['user_id'] = $this->isLoginInfo->id;
                $pointParams['ptype'] = 'plus';
                $pointParams['point'] = $orderInfo->reservePoint; // 포인트
                $pointParams['oid'] = $orderInfo->id; // 주문고유키
                $pointParams['pcode'] = 'OC';
                $pointResult = $this->pointRepository->insertPoint($pointParams);
                if(!empty($pointResult))$this->memberRepository->updateMemberPoint($pointParams);
            }
            $status = 'success';
            // 이벤트 정보 (주문히스토리)
            $eventParams['nowstatus'] = 'OC';
            $eventParams['oldstatus'] = $ocIdsInfo;
            \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));
        }
        return ['status'=>$status,'data'=>''];
    }
    // 클레임 체크
    public function getClaimCheckProductList(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $claimType = $request->input('claimType');
        $data['orderList'] =  $this->orderRepository->getMyOrderDetail($this->isLoginInfo->id,$request->input('oid'));
        $errorFlag = false;
        foreach($data['orderList'] as $val) {
            switch($claimType) {
                case 'CR': // 취소요청
                    if($val->ordStatus != 'notpay' && $val->ordStatus != 'income') {
                        $errorFlag = true;
                    }
                break;
                case 'ER': // 교환요청
                case 'RR': // 반품요청
                    if($val->ordStatus != 'DC') {
                        $errorFlag = true;
                    }
                break;
            }
        }
        if($errorFlag) {
            $status = 'error';
            $data='';
        } else {
            $status = 'success';
            $data['pointName'] = ($this->pointConfig)?$this->pointConfig->pointName:'';
            $data['pointUnit'] = ($this->pointConfig)?$this->pointConfig->pointUnit:'';
            $data['paymethods'] = config('order.paymethods');
            if(!empty($data['orderList'][0]->useCouponId)) {
                $data['couponInfo'] = $this->couponRepository->getCouponInfoByOrder($data['orderList'][0]->useCouponId,$this->isLoginInfo->id);
            }
            switch($claimType) {
                case 'CR': // 취소요청
                    break;
                case 'RR': // 반품요청
                    if($data['orderList'][0]->serviceType=='normal') {
                        $data['deliveryInfo'] = $this->deliveryRepository->getDeliveryInfo($data['orderList'][0]->deliveryId);
                    }
                    break;
                case 'ER': // 교환요청
                    break;
            }

        }
        return ['status'=>$status,'data'=>$data];
    }

    // 클레임 저장
    public function insertOrderClaim(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }


        $claimType = $request->input('claimType'); // 클레임 타입
        $changeStatus = $claimType; // 변경할 클레임상태
        $oldStatus = ''; // 기존 상태
        $insertDeny = false;
        $productList =  $this->orderRepository->getMyOrderProductListByIds($this->isLoginInfo->id,$request->input('opIds'));
        $opIdsInfo = [];
        $orderInfo = '';
        foreach($productList as $val) {
            $opIdsInfo[$val->id] = ['ostatus'=>$val->ostatus,'oid'=>$val->oid]; // 주문상품에 변경할 데이타를 저장한다
            if(!$orderInfo)$orderInfo = $val;
            if(!$oldStatus)$oldStatus = $val->ostatus;
            switch($claimType) { // 변경할 클레임과 현재 상태비교
                case 'CR': //주문취소요청 (미입금 , 입금완료 일대만 가능 ,notpay,income)
                    switch($val->ostatus) {
                        case 'notpay': // 미입금 시에는 곧장 취소완료 처리한다
                            $changeStatus = 'CC';
                        break;
                        case 'income':

                        break;
                        default:
                            $insertDeny = true;
                        break;
                    }
                break;
                case 'ER': //교환요청
                case 'FR'://환불요청
                case 'RR': //반품요청 (배송준비,배송중,배송완료 시에만 가능)
                    if($val->ostatus != 'DC') {
                        $insertDeny = true; // 변경불가
                    }
                break;
                default:
                    $insertDeny = true;
                break;
           }
        }
        if($insertDeny) {
            return ['status'=>'message','data'=>'changeDeny'];
        }
        if($changeStatus == 'CC' && $orderInfo) {
            if($orderInfo->useCouponId || $orderInfo->usePoint) {
                $recoverParams['oid'] = $orderInfo->oid;
                $recoverParams['useCouponId'] = $orderInfo->useCouponId;
                $recoverParams['useCouponPrice'] = $orderInfo->useCouponPrice;
                $recoverParams['usePoint'] = $orderInfo->usePoint;
                $recoverParams['pcode'] = $changeStatus;

                $this->recoverService->discountRecoverAll($recoverParams,$this->isLoginInfo->id);
            }
            $this->inventoryUpdate($orderInfo->oid);
        }
        $data = '';
        $change =  $this->orderRepository->updateMyOrderClaimStatus($request->input('opIds'),$changeStatus);
        if($change) { // 클레임 데이타 저장 한다
            $params = $request->all();
            $params['user_code'] = $this->isLoginInfo->id;
            $params['oldOstatus'] = $oldStatus;
            $params['claimType'] = $changeStatus;

            $claimFieldset = makeFieldset($this->orderClaimRepository->useFields,$params);
            // 기존에 클레임 상태가 존재 했었는지 체크
            $isData = $this->orderClaimRepository->isClaimData($params['oid']);
            if($isData && $isData->id) {
                return ['status'=>'fail','data'=>'']; // 에러처리함
            }
            $claimInfo = $this->orderClaimRepository->insertClaim($claimFieldset);
            if($claimInfo && $claimInfo->id) {
                foreach($opIdsInfo as $opId => $idData) {
                    $prdparams['opid'] = $opId;
                    $prdparams['oldOstatus'] = $idData['ostatus']; // 기존 주문상태 저장
                    $prdparams['claim_id'] = $claimInfo->id;
                    $fieldset = makeFieldset($this->orderClaimRepository->productUseFields,$prdparams);
                    $data = $this->orderClaimRepository->insertClaimProduct($fieldset);

                }
                if($data && $claimType != 'CR' && !empty($request->input('ocIds'))) { // 선택되지 않은 정보는 모두 구매확정 시킨다
                    $ocIds = $request->input('ocIds');
                    if(count($ocIds)>0) {
                        $this->orderRepository->updateMyOrderStatus($ocIds,'OC');

                        $ocIdsInfo = [];
                        foreach($ocIds as $opid) {
                            $ocIdsInfo[$opid] = ['ostatus'=>'DC','oid'=>$orderInfo->oid]; // 주문상품에 변경할 데이타를 저장한다

                        }
                         // 이벤트 정보 (주문히스토리)
                        $eventParams['nowstatus'] = 'OC';
                        $eventParams['oldstatus'] = $ocIdsInfo;
                        \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));
                        $eventParams = [];
                    }

                }
            }
        }
        if($data) {
            // 이벤트 정보 (주문히스토리)
            $eventParams['nowstatus'] = $changeStatus;
            $eventParams['oldstatus'] = $opIdsInfo;
            \Event::dispatch(new \App\Events\OrderHistoryEvent($eventParams));

            return ['status'=>'success','data'=>$data];
        } else {
            return ['status'=>'fail','data'=>''];
        }

    }

    /// 상품 재고  수정
    private function inventoryUpdate(int $oid) {

        $list = $this->orderRepository->getOrderProductListByOid($oid);
        $productList = [];
        foreach($list as $data) {
            if(!empty($data->optionSingleInfos)) {
                $optionSingleInfos = json_decode($data->optionSingleInfos);
                foreach($optionSingleInfos as $option) {
                    if($option->required == 'N') {

                        $productList[] = ['pid'=>$data->pid,'opt_id'=>$option->opt_id,'amt'=>$option->camt,'invAmt'=>$option->invAmt];
                    }
                }
            }
            $optId = (!empty($data->opt_id))?$data->opt_id:'';
            $productList[] = ['pid'=>$data->pid,'opt_id'=>$optId,'amt'=>$data->oamt,'invAmt'=>$data->tempInvAmt];
        }
        foreach($productList as $product) {
            $this->updateInventoryAction($product);
        }
    }
    private function updateInventoryAction($data) {
        if(!empty($data['opt_id'])) {
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByOid($data['opt_id']);
            if(!$inventoryInfo)return;

            $params['amt'] = $inventoryInfo->able_amt + $data['amt'];
            $this->productRepository->updateProductOption($data['opt_id'],$params);


        } else if(!empty($data['pid'])) {
            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByPid($data['pid']);
            if(!$inventoryInfo)return;

            $params['amt'] = $inventoryInfo->able_amt + $data['amt'];
            $this->productRepository->updateProduct($data['pid'],$params);

        } else {
            return;
        }


        $addAmt = $data['amt'];

        $inventoryParams = [];
        $able_amt = $inventoryInfo->able_amt + $addAmt;  // 판매가능한 재고
        $disable_amt = $inventoryInfo->disable_amt - $addAmt; // 판매 불가능한 재고

        $inventoryParams['able_amt'] = $able_amt;
        $inventoryParams['disable_amt'] = $disable_amt;
        $this->productInventoryRepository->updateInventoryProduct($inventoryParams,$inventoryInfo->id);
        // 이벤트 정보 (재고히스토리)
        $eventParams['ivt_id'] = $inventoryInfo->id;
        $eventParams['type'] = 'notpayCC';
        $eventParams['content'] = ['addAmt'=>$addAmt,'able_amt'=>$able_amt,'disable_amt'=>$disable_amt];
        \Event::dispatch(new \App\Events\InventoryHistoryEvent($eventParams));

    }
}
