<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiOrderRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiOrderProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiCartRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiCouponRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiShippingRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductInventoryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiPointRepository;


use App\Services\Api\Core\Common\CommonOrderCartService;
use App\Services\Api\Core\Common\CommonOrderCouponService;
use App\Services\Api\Core\Common\CommonOrderPointService;
use App\Services\Api\Core\Common\CommonOrderDeliveryService;

use App\Events\MailEvent;
use App\Events\SmsEvent;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiOrderService extends CoreApiAuthHeader {

    protected $orderRepository;
    protected $orderProductRepository;
    protected $cartRepository;
    protected $couponRepository;
    protected $shippingRepository;
    protected $productInventoryRepository;
    protected $productRepository;
    protected $memberRepository;
    protected $pointRepository;

    protected $comOrdCartService;
    protected $comOrdCouponService;
    protected $comOrdPointService;
    protected $comOrdDeliveryService;


    public function __construct(Request $request,
                                CommonOrderCartService $comOrdCartService,
                                CommonOrderCouponService $comOrdCouponService,
                                CommonOrderPointService $comOrdPointService,
                                CommonOrderDeliveryService $comOrdDeliveryService,
                                CustomizeApiOrderRepository $orderRepository,
                                CustomizeApiOrderProductRepository $orderProductRepository,
                                CustomizeApiProductInventoryRepository $productInventoryRepository,
                                CustomizeApiCartRepository $cartRepository,
                                CustomizeApiCouponRepository $couponRepository,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiMemberRepository $memberRepository,
                                CustomizeApiPointRepository $pointRepository,
                                CustomizeApiShippingRepository $shippingRepository) {

        parent::__construct($request);

        $this->orderProductRepository = $orderProductRepository;
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
        $this->couponRepository = $couponRepository;
        $this->shippingRepository = $shippingRepository;
        $this->productInventoryRepository = $productInventoryRepository;
        $this->productRepository = $productRepository;
        $this->memberRepository = $memberRepository;
        $this->pointRepository = $pointRepository;

        $this->comOrdCartService = $comOrdCartService;
        $this->comOrdCouponService = $comOrdCouponService;
        $this->comOrdPointService = $comOrdPointService;
        $this->comOrdDeliveryService = $comOrdDeliveryService;


    }


    // 주문시도시 정보 불러오기
    public function orderRegistInfo(Request $request) {

        $cartIds = $request->input('ids');
        $user_code = ($this->isLoginInfo && $this->isLoginInfo->id)?$this->isLoginInfo->id:$this->noMemberId;

        $type = ($request->input('type') == 'direct')?'temp':'base';

        $cartList = $this->cartRepository->getUserCartListByIds($user_code,$cartIds,$type);
        $cartData = $this->comOrdCartService->getCartParseDatas($cartList);


        if($this->isLoginInfo && $this->isLoginInfo->id) { // 로그인한 회원인 경우
            $resultData['couponList'] = $this->getUserAbleCouponList($cartData);
            $resultData['pointSetting'] = $this->siteInfos['points'];
            $resultData['havePoint'] = $this->isLoginInfo->point;
            $resultData['totalMaxUsePoint'] = $cartData['totalMaxUsePoint'];
            if($cartData['serviceType'] == 'normal') {
                $resultData['shippingList'] = $this->shippingRepository->getShippingList($this->isLoginInfo->id);
            }


        }

        $deliveryPrice = 0;
        if($cartData['serviceType'] == 'normal') {
            $deliveryInfo = $this->comOrdCartService->getOrderDeliveryInfo($cartData,$this->siteInfos['delivery']);
            $resultData['deliveryInfo'] = $deliveryInfo;
            $deliveryPrice = $deliveryInfo['deliveryPrice'];
        }
        $resultData['cartList'] = $cartData['data'];
        $resultData['totalPrice'] = $cartData['totalPrice'];
        $resultData['totalOrderAmt'] = $cartData['totalOrderAmt'];
        $resultData['serviceType'] = $cartData['serviceType'];
        $resultData['totalReservePoint'] = $cartData['totalReservePoint'];
        $resultData['totalPaymentPrice'] = $cartData['totalPrice'] + $deliveryPrice;
        $resultData['orderSetting'] = $this->siteInfos['order'];
        $resultData['paymethodNames'] = config('order.paymethods');

        return ['status'=>'success','data'=>$resultData];

    }

    public function updateOrderPriceInfo(Request $request) {
        $user_code = ($this->isLoginInfo && $this->isLoginInfo->id)?$this->isLoginInfo->id:$this->noMemberId;

        return $this->getOrderPriceInfo($request,$user_code);
    }
    private function getOrderPriceInfo(Request $request,$user_code) {

        $cartIds = $request->input('ids');
        $type = ($request->input('type') == 'direct')?'temp':'base';

        $cartList = $this->cartRepository->getUserCartListByIds($user_code,$cartIds,$type);

        $cartData = $this->comOrdCartService->getCartParseDatas($cartList);
        $totalPrice = $cartData['totalPrice']; // 최종결제금액
        $totalGoodsPrice = $cartData['totalPrice']; // 최종상품할인금액
        $totalMaxUsePoint = $cartData['totalMaxUsePoint']; // 사용가능한 최대 적립금

        $useCouponPrice = 0;
        $couponError = '';
        $usePointPrice = 0;
        $pointError = '';
        $denyReservePoint = 'no'; //yes 적립제외함 , 제한없음 (포인트 적립을 제외하냐 마냐)
        if($this->isLoginInfo && $this->isLoginInfo->id) {

            $pointSetting = $this->siteInfos['points'];
            $denyReservePointSetting = ['usePoint'=>'no','useCoupon'=>'no'];
            if($pointSetting->denyPoint) {
                foreach($pointSetting->denyPoint as $pt) {
                    if($pt=='pp') {
                        $denyReservePointSetting['usePoint'] = 'yes';
                    } else if($pt=='cp') {
                        $denyReservePointSetting['useCoupon'] = 'yes';
                    }
                }
            }

            /// 쿠폰 사용시
            $denyReservePoint = 'no';
            if(!empty($request->input('useCoupon'))) {
                $couponInfo = $this->couponRepository->getUserCouponInfo($request->input('useCoupon'),$this->isLoginInfo->id);
                if($couponInfo) {
                   $useCouponPriceInfo = $this->comOrdCouponService->getUseCouponPrice($couponInfo,$cartData);
                   if($useCouponPriceInfo) {
                        $useCouponPrice = $useCouponPriceInfo['useCouponPrice'];
                        $totalPrice = $useCouponPriceInfo['totalPrice'];
                        $totalGoodsPrice = $useCouponPriceInfo['totalPrice'];
                        $pointDeny = $useCouponPriceInfo['pointDeny'];
                        if($denyReservePointSetting['useCoupon']== 'yes') {// 포인트 적립 금지
                            $denyReservePoint = 'yes';
                        } else {
                            if($pointDeny=='yes')$denyReservePoint = 'yes';// 포인트 적립 금지
                        }

                   } else {
                    $couponError= 'notCoupon';
                   }
                } else {
                    $couponError= 'notCoupon';
                }

            }
            if(!empty($request->input('usePoint'))) { // 포인트 사용시
                $pointResult = $this->comOrdPointService->getUserPointPrice($request->input('usePoint'),
                                                                            $this->isLoginInfo->point,
                                                                            $this->siteInfos['points'],
                                                                            $totalPrice,
                                                                            $totalMaxUsePoint);

                if($pointResult['result'] == 'success') {
                    $usePointPrice = $pointResult['point'];
                    $totalPrice = (int)$totalPrice - (int)$usePointPrice;
                    if($denyReservePointSetting['usePoint']== 'yes') { // 포인트 적립 금지
                        $denyReservePoint = 'yes';
                    }
                } else {
                    $pointError = $pointResult['result'];
                }

            }
        }
        $status = 'success';
        $deliveryResult = [];
        if($cartData['serviceType'] == 'normal') {
            if(!empty($request->input('deliveryId'))) { // 배송비 사용일경우
                $params = $request->all();
                $deliveryResult = $this->comOrdDeliveryService->getDeliveryPrice($params,$totalGoodsPrice);
                if(!$deliveryResult)$status = 'error';
            } else $status = 'error';
        }
        return ['status'=>$status,
                'data'=>[
                    'totalPrice'=>$totalPrice, // 실제 결제할 금액
                    'useCouponPrice'=>$useCouponPrice,
                    'pointError'=>$pointError,
                    'couponError'=>$couponError,
                    'usePointPrice'=>$usePointPrice,
                    'denyReservePoint'=>$denyReservePoint,
                    'totalReservePoint'=>$cartData['totalReservePoint'],
                    'deliveryResult'=>$deliveryResult
                    ]
               ];

     }


    // 주문정보 저장
    public function insertOrder(Request $request) {

        $requestParams = $request->all();


        if($this->isLoginInfo && $this->isLoginInfo->id) {
            $user_code = $this->isLoginInfo->id;
            $is_member = 'yes';
        } else {
            $user_code = $this->noMemberId;
            $is_member = 'no';
        }
        // 유효성 검증
        $priceInfo = $this->getOrderPriceInfo($request,$user_code);
        if($priceInfo['status'] != 'success') {
            return ['status'=>'error','data'=>''];
        } else {
            if($priceInfo['data']['pointError'] || $priceInfo['data']['couponError']) {
                $message = ($priceInfo['data']['pointError'])?$priceInfo['data']['pointError']:$priceInfo['data']['couponError'];
                return ['status'=>'message','data'=>$message];
            }
        }
        if($request->input('serviceType') == 'normal') {
            $totalPaymentPrice = $priceInfo['data']['totalPrice'] + $priceInfo['data']['deliveryResult']['resultPrice'];
        } else {
            $totalPaymentPrice = $priceInfo['data']['totalPrice'];
        }
        if($totalPaymentPrice != $request->input('totalPrice')) {
            return ['status'=>'message','data'=>'notEqualPrice'];
        }

        // 유효성 검증후에 데이타 가공
        $fieldset = makeFieldset($this->orderRepository->useFields,$requestParams);

        $fieldset['user_code'] = $user_code;
        $fieldset['is_member'] = $is_member;
        if($request->input('serviceType') == 'normal') {
            $fieldset['deliveryPrice'] = $priceInfo['data']['deliveryResult']['resultPrice']; // 배송비

            if($priceInfo['data']['deliveryResult']['addPrice']) {
                $fieldset['localDeliveryPrice'] = $priceInfo['data']['deliveryResult']['addPrice']; // 지역별 추가배송비
            }
        } else $fieldset['deliveryPrice'] = 0;

        $productCouponPid = '';
        if($is_member == 'yes') {
            /// 쿠폰정보
            if(!empty($requestParams['useCoupon'])) {
                $fieldset['useCouponId'] = $requestParams['useCoupon'];
                $fieldset['useCouponPrice'] = $priceInfo['data']['useCouponPrice'];
                if(!empty($this->comOrdCouponService->productCouponPid)) {
                    $productCouponPid = $this->comOrdCouponService->productCouponPid;
                }
            }
            // 적립금 정보
            if(!empty($priceInfo['data']['usePointPrice'])) {
                $fieldset['usePoint'] = $priceInfo['data']['usePointPrice'];
            }
            if($priceInfo['data']['denyReservePoint'] == 'no') { // 포인트 적립 가능여부
                $fieldset['reservePoint'] = $priceInfo['data']['totalReservePoint'];
            }
             if($request->input('serviceType') == 'normal') {
                // 배송지 직접 입력일 경우
                if(empty($request->input('shippingId'))) {
                    $requestParams['title'] = $requestParams['shippingTitle'];
                    $shippingFieldset = makeFieldset($this->shippingRepository->useFields,$requestParams);
                    $shippingFieldset['user_id'] = $user_code;
                    $this->shippingRepository->insertShipping($shippingFieldset);
                }
            }
        }
        $fieldset['order_code'] = mt_rand(1000000,9999999).'_'.mt_rand(1000000,9999999);//주문번호 생성
        $fieldset['ostatus'] = 'notpay';
        $fieldset['oamt'] = 1;
        $fieldset['price'] = $totalPaymentPrice;

        $orderInfo = $this->orderRepository->insertOrder($fieldset);
        if($orderInfo && $orderInfo->id) {
            $cartIds = $request->input('ids');
            $type = ($request->input('type') == 'direct')?'temp':'base';

            $cartList = $this->cartRepository->getUserCartListByIds($user_code,$cartIds,$type);
            $cartData = $this->comOrdCartService->getCartParseDatas($cartList);
            $messagePname = '';
            $messageAmt = 0;
            foreach($cartData['data'] as $cart) { // 카트정보 가져오기
                if($cart->status != 'sale')continue;
                if(!$messagePname) {
                    $messagePname = $cart->pname;
                } else {
                    $messageAmt = $messageAmt + $cart->camt;
                }
                $productParams = makeFieldset($this->orderProductRepository->useFields,$cart);
                $productParams['payprice'] = $cart->sellprice;
                $productParams['oamt'] = $cart->camt;
                $productParams['oid'] = $orderInfo->id;
                $productParams['ostatus'] = 'notpay';
                if($productCouponPid && $productCouponPid== $productParams['pid']) {
                    $productParams['couponId'] = $requestParams['useCoupon'];
                }
                $productParams['tempInvAmt'] = $cart->gamt; // 임시 저장된 현재 재고
                if($this->isLoginInfo && $this->isLoginInfo->id) {
                    $productParams['user_id'] = $this->isLoginInfo->id;
                }
                if($cart->optionUse=='yes' && $cart->optionType=='multi') { // 조합형 옵션
                    $productParams['opt_id'] = $cart->option_id;
                    $productParams['opt_name'] = $cart->option_name;
                    $this->orderProductRepository->insertOrderProduct($productParams);

                } else if($cart->optionUse=='yes' && $cart->optionType=='single') { // 단독형 옵션
                    if($cart->optionRequired == 'Y') {
                        $productParams['opt_id'] = $cart->optionId;
                    } else $productParams['opt_id'] = '';

                    $productParams['opt_name'] = $cart->option_name;
                    if(!empty($cart->optionResult)) {
                        $productParams['optionSingleInfos'] = json_encode($cart->optionResult);
                    }
                    $this->orderProductRepository->insertOrderProduct($productParams);

                } else { // 옵션정보 없을 경우
                    $productParams['opt_id'] = '';
                    $productParams['opt_name'] = '';
                    $productParams['opt_code'] = '';
                    $this->orderProductRepository->insertOrderProduct($productParams);

                }
            }
            // 회원일 경우 쿠폰사용 포인트 사용정보 저장
            if($is_member == 'yes') {
                /// 쿠폰정보
                if(!empty($orderInfo->useCouponId)) {
                    $this->couponRepository->useCoupon($orderInfo->useCouponId,'yes');
                }
                 if(!empty($orderInfo->usePoint)) {
                    $ptParams['user_id'] = $this->isLoginInfo->id;
                    $ptParams['ptype'] = 'minus';
                    $ptParams['point'] = $orderInfo->usePoint;
                    $ptParams['oid'] = $orderInfo->id;
                    $ptParams['pointMsg'] = $this->pointRepository->PCODES['order'];
                    $ptParams['pcode'] = 'order';
                    $this->pointRepository->insertPoint($ptParams);
                    $this->memberRepository->updateMemberPoint($ptParams);
                }
            }
            if($request->input('paymethod') == 'bank') {
                $this->inventoryUpdate($orderInfo->id); // 재고정보 수정


                $eventParams['orderDate'] = date('Y-m-d');
                $eventParams['userName']  = $requestParams['oname'];
                if($is_member=='yes') {
                    $eventParams['userId']  = $this->isLoginInfo->id;
                }
                if($messageAmt>0) {
                    $eventParams['orderInfo'] = $messagePname.' 외 '.$messageAmt.'개';
                } else {
                    $eventParams['orderInfo'] = $messagePname;
                }

                $msgpaymentInfo = '결제방법:무통장';

                if($request->input('serviceType') == 'normal') {
                    $msgdeliveryPrice = ($requestParams['deliveryPrice'])?number_format($requestParams['deliveryPrice']):0;
                    $msgpaymentInfo.= ' 배송비:'.$msgdeliveryPrice.'원';
                }
                $totalPaymentPrice = ($totalPaymentPrice)?number_format($totalPaymentPrice):0;
                $msgpaymentInfo.= ' 결제금액:'.$totalPaymentPrice.'원';
                $eventParams['paymentInfo'] = $msgpaymentInfo;
                if(!empty($this->siteInfos['order']) && !empty($this->siteInfos['order']->bankSetting)) {
                    $expireDay = $this->siteInfos['order']->bankSetting->expireDay;
                    $eventParams['bankExpire'] = date('Y-m-d',mktime(1,1,1,date('m'),date('d')+$expireDay,date('Y')));
                    $eventParams['accountInfo'] = '입금계좌정보 :'.$this->siteInfos['order']->bankSetting->bankName.' '.$this->siteInfos['order']->bankSetting->bankAccount.' '.$this->siteInfos['order']->bankSetting->bankOwner;

                }
                $eventParams['type'] = 'notpay';

                if(!empty($requestParams['oemail'])) {
                    $eventParams['to'] = $requestParams['oemail'];
                    \Event::dispatch(new \App\Events\MailEvent($eventParams));
                }
                if(!empty($requestParams['opcs'])) {
                    $eventParams['to'] = $requestParams['opcs'];
                    \Event::dispatch(new \App\Events\SmsEvent($eventParams));
                }
            }
            $this->deleteCartByOrderComplete($request);
            return ['status'=>'success','data'=>$orderInfo];
        } else {
            $status = 'error';
            return ['status'=>$status,'data'=>''];
        }


    }
    private function deleteCartByOrderComplete(Request $request) {

        $cartIds = $request->input('ids');
        $type = ($request->input('type') == 'direct')?'temp':'base';
        if($this->isLoginInfo && $this->isLoginInfo->id) {
            $user_code = (string)$this->isLoginInfo->id;
        } else {
            $user_code = $this->noMemberId;
        }
        $this->cartRepository->deleteUserCartListByIds($user_code,$cartIds,$type);
    }
    /// 사용가능한 쿠폰 리스트
    private function getUserAbleCouponList($cartData) {
        $resultAbleCouponList = [];
        $couponList = $this->couponRepository->getUserCouponList($this->isLoginInfo->id);
        foreach($couponList as $cpndata) {
            $data = $this->comOrdCouponService->getUserCouponCheck($cpndata,$cartData);
            if($data)$resultAbleCouponList[] = $data;

        }
        return $resultAbleCouponList;

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
            $params['amt'] = $data['invAmt'] - $data['amt']; // 재고수량
            $this->productRepository->updateProductOption($data['opt_id'],$params);

            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByOid($data['opt_id']);
        } else if(!empty($data['pid'])) {
            $params['amt'] = $data['invAmt'] - $data['amt']; // 재고수량
            $this->productRepository->updateProduct($data['pid'],$params);

            $inventoryInfo =  $this->productInventoryRepository->getInventoryInfoByPid($data['pid']);
        } else {
            return;
        }
        if(!$inventoryInfo)return;

        $addAmt = $data['amt'];

        $inventoryParams = [];
        $able_amt = $inventoryInfo->able_amt - $addAmt;  // 판매가능한 재고
        $disable_amt = $inventoryInfo->disable_amt + $addAmt; // 판매 불가능한 재고

        $inventoryParams['able_amt'] = $able_amt;
        $inventoryParams['disable_amt'] = $disable_amt;
        //$inventoryParams['manger_code'] = $params->input('manger_code'); // 관리코드
        $this->productInventoryRepository->updateInventoryProduct($inventoryParams,$inventoryInfo->id);
        // 이벤트 정보 (재고히스토리)
        $eventParams['ivt_id'] = $inventoryInfo->id;
        $eventParams['type'] = 'disable';
        $eventParams['content'] = ['addAmt'=>$addAmt,'able_amt'=>$able_amt,'disable_amt'=>$disable_amt];
        \Event::dispatch(new \App\Events\InventoryHistoryEvent($eventParams));

    }


     // 주문완료
     public function getOrderComplete(Request $request) {

        if(empty($request->input('oid'))) {
            return ['status'=>'emptyField','data'=>''];
        }
        if($this->isLoginInfo && $this->isLoginInfo->id) {
            $user_code = $this->isLoginInfo->id;
        } else {
            $user_code = $this->noMemberId;
        }

        $oid = $request->input('oid');
        $data['orderList'] =  $this->orderRepository->getMyOrderDetail($user_code,$oid);
        $data['paymethodNames'] = config('order.paymethods');
        $data['orderSetting'] = $this->siteInfos['order'];
        return ['status'=>'success','data'=>$data];
     }
}
