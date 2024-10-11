<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiPointRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiOrderRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiOrderReviewRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiMemberRepository;


use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;


class CoreApiReviewService extends CoreApiAuthHeader {

    protected $orderRepository;
    protected $pointRepository;
    protected $orderReviewRepository;
    protected $memberRepository;

    public function __construct(Request $request,CustomizeApiPointRepository $pointRepository,
                                CustomizeApiOrderRepository $orderRepository,
                                CustomizeApiMemberRepository $memberRepository,
                                CustomizeApiOrderReviewRepository $orderReviewRepository) {

        parent::__construct($request);

        $this->orderRepository = $orderRepository;
        $this->pointRepository = $pointRepository;
        $this->orderReviewRepository = $orderReviewRepository;
        $this->memberRepository = $memberRepository;
    }


    // 리뷰 등록가능한 주문목록
    public function getMyAbleReviewOrderList(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        $params = $request->all();
        $orderList = $this->orderRepository->getMyAbleReviewOrderList($this->isLoginInfo->id,$params);
        $data = [];
        foreach($orderList as $order) {

            $row = $this->orderReviewRepository->getIsOrderReview($this->isLoginInfo->id,$order->oid,$order->pid);
            if(!$row) {
                $data[] = $order;
            }
        }
        return ['status'=>'success','data'=>$data];
    }
    // 나의 구매후기 목록
    public function getMyReviewList(Request $request) {
         if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
         }
         $params = $request->all();
         $reviewList = $this->orderReviewRepository->getMyReviewList($this->isLoginInfo->id,$params);
         foreach($reviewList as $val) {
            $val->productList = $this->orderRepository->getOrderProductListByOidPid($val->oid,$val->pid);
         }


         return ['status'=>'success','data'=>$reviewList];
    }
    // 상품 리뷰 목록
    public function getProductReviewList(Request $request) {

        $limit = (!empty($request->input('limit')))?$request->input('limit'):20;
        $data = $this->orderReviewRepository->getReviewListByPid($request->input('pid'),$limit);
        return ['status'=>'success','data'=>$data];
    }

    // 상품 리뷰 정보
    public function getMyReviewInfo(Request $request) {
        if(empty($this->isLoginInfo)) {
             return ['status'=>'notLogin','data'=>''];
         }
        if(!$request->has(['oid','pid'])) {
            return ['status'=>'emptyField','data'=>''];
        }

        $info = $this->orderReviewRepository->getIsOrderReview($this->isLoginInfo->id,$request->input('oid'),$request->input('pid'));
        $productList = $this->orderRepository->getMyOrderProductList($this->isLoginInfo->id,$request->input('oid'));
        $auth = false;
        $goodsList = [];
        foreach($productList as $product) {
            if($product->pid == $request->input('pid')) {
                if($product->ostatus == 'OC') {
                    $auth = true;
                    $goodsList[] = $product;
                }
            }

        }
        return ['status'=>'success','data'=>['info'=>$info,'goodsList'=>$goodsList,'point'=>$this->siteInfos['points']]];
    }

    // 구매후기 저장
    public function insertOrderReview(Request $request) {
        if(empty($this->isLoginInfo)) {
            return ['status'=>'notLogin','data'=>''];
        }
        if (!$request->has(['oid','pid','content','grade'])) {
            return ['status'=>'error','data'=>''];
        }
        // 기존에 후기글이 있는지 체크
        $isInfo = $this->orderReviewRepository->getIsOrderReview($this->isLoginInfo->id,$request->input('oid'),$request->input('pid'));
        if(!empty($isInfo)) {
            return ['status'=>'message','data'=>'is'];
        }
        // 현재 구매상품의 주문상태 체크
        $productList = $this->orderRepository->getMyOrderProductList($this->isLoginInfo->id,$request->input('oid'));
        foreach($productList as $product) {
            if($product->pid == $request->input('pid')) {
                $ostatusList[$product->ostatus] = $product->ostatus;
            }

        }
        // 설정 정보에서 값을 가져옴(글을 쓰는 상태가 가능한 주문상태)
        $authStatus = 'OC'; // 임시
        if(empty($ostatusList[$authStatus])) {
            return ['status'=>'message','data'=>'denyOstatus'];
        }
        // 설정 정보에서 값을 가져옴(후기 작성시 증정 적립금)
        $reservePoint = '';
        if(!empty($this->siteInfos['points'])) {
            $pointset = $this->siteInfos['points'];
            if(!empty($pointset->oreplyPointUse) && $pointset->oreplyPointUse == 'yes') {
                if(!empty($request->input('tempImgs'))) {
                    $reservePoint = (int)$pointset->oreplyPhotoPoint;
                } else {
                    $reservePoint = (int)$pointset->oreplyPoint;
                }
            }
        }

        //후기 글 저장
        $fieldset = makeFieldset($this->orderReviewRepository->useFields,$request->all());
        $fieldset['user_id'] = $this->isLoginInfo->id;

        if(!empty($reservePoint))$fieldset['point'] = $reservePoint;
        if(!empty($request->input('tempImgs'))) {
            $imgData = [];
            $uploadParams = [];
            $uploadParams['type'] = ['image'];
            if(!empty($this->siteInfos['images']) && !empty($this->siteInfos['images']->prlist)) {
                $uploadParams['resize'] = ['width'=>$this->siteInfos['images']->prlist->width,'height'=>$this->siteInfos['images']->prlist->height];
            } else {
                $uploadParams['resize'] = ['width'=>640,'height'=>640];
            }

            foreach($request->input('tempImgs') as $key=>$val) {
                $fileIndex = $key+1;
                $imgName = uploadFile($request,'img'.$fileIndex,$this->orderReviewRepository->filePath,$uploadParams);
                if(empty($imgName)) {
                   return ['status'=>'message','data'=>'wrong_img'];
                }
               $imgData[] = $this->orderReviewRepository->imgUrl.$imgName;
            }
            $fieldset['imgs'] = json_encode($imgData);
        }
        $data = $this->orderReviewRepository->insertOrderReview($fieldset);
        if($data && !empty($reservePoint)) { // 포인트 증정
                $pointParams['pointMsg'] = $this->pointRepository->PCODES['review'];
                $pointParams['user_id'] = $this->isLoginInfo->id;
                $pointParams['ptype'] = 'plus';
                $pointParams['point'] = $reservePoint; // 포인트
                $pointParams['oid'] = $request->input('oid'); // 주문고유키
                $pointParams['pcode'] = 'review';
                $pointResult = $this->pointRepository->insertPoint($pointParams);
                if(!empty($pointResult))$this->memberRepository->updateMemberPoint($pointParams);
        }
        return ['status'=>'success','data'=>$data];
    }
}
