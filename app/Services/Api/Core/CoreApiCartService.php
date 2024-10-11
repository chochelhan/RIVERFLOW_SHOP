<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiCartRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Services\Api\Core\Common\CommonOrderCartService;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiCartService extends CoreApiAuthHeader {

    protected $cartRepository;
    protected $productRepository;
    protected $commonService;

    public function __construct(Request $request,
                                CustomizeApiCartRepository $cartRepository,
                                CustomizeApiProductRepository $productRepository,
                                CommonOrderCartService $commonService) {
        parent::__construct($request);

        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->commonService = $commonService;


        //$this->isLoginInfo;
    }

    // 임시 장바구니 저장
    public function insertTempCart(Request $params) {
        $requestParams = $params->all();
        return $this->cartInAction('temp',$requestParams);
    }

    // 장바구니 저장
    public function insertCart(Request $params) {
        $requestParams = $params->all();
        return $this->cartInAction('base',$requestParams);
    }
    /**
    *@ return error params
    *   noRequiredOption => 필수 옵션값 없음
    *   soldout => 품절
    *   reload => 판매 불가능 옵션존재
    *   overBuy => 판매가능한 재고보다 구매수량이 많음
    *  noDeliveryGroup => 묶음배송 불가
    **/
    private function cartInAction(string $ctype,array $requestParams) {

        $pid = $requestParams['pid'];
        $productInfo = $this->productRepository->getProductInfo($pid);
        if($productInfo->optionType=='multi' && empty($requestParams['option_info'])) {
            return ['status'=>'message','data'=>['type'=>'noRequiredOption']];
        }

        if($productInfo->deliveryGroup=='no' && $ctype!='temp') { // 묶음배송 가능여부 체크
            return ['status'=>'message','data'=>['type'=>'noDeliveryGroup']];
        }
        $requiredOptionIds = [];
        if($productInfo->optionType=='single') { // 단독형 옵션일때
             $requiredOptions = $this->cartRepository->getOptionInfoByPidWithRequired($pid);
             if($requiredOptions && count($requiredOptions)>0) {
                if(empty($requestParams['option_info'])) { // 필수옵션이 존재할때 옵션값 없을경우
                    return ['status'=>'message','data'=>['type'=>'noRequiredOption']];
                }
                foreach($requiredOptions as $val)$requiredOptionIds[$val->id] = $val->id;
             }

        }
        if(!empty($requestParams['option_info'])) { // 옵션을 사용할 경우

            $optData = json_decode($requestParams['option_info']);
            $optIds = [];
            $optDatas = [];
            $requiredOptionFlag = false;
            $requiredOptionCamt = 0;
            $requiredOptionId = '';
            foreach($optData as $opt) {

                $optIds[] = $opt->id;
                $optDatas[$opt->id] = $opt->camt;
                if($productInfo->optionType=='single' && count($requiredOptionIds)>0) { // 단독형 옵션일때
                    if(!empty($requiredOptionIds[$opt->id])) { // 필수 옵션이 있을경우
                        $requiredOptionFlag = true;
                        $requiredOptionCamt = $opt->camt;
                        $requiredOptionId = $opt->id;
                    }
                }
            }
            if($productInfo->optionType=='single' && count($requiredOptionIds)>0  && !$requiredOptionFlag) { // 단독형 옵션 필수옵션값이 존재할때 옵션정보에 없으면
                  return ['status'=>'message','data'=>['type'=>'noRequiredOption']];
            }
            ////// 재고 및 옵션 사용여부 체크
            $errorType = '';
            $optionInfos = $this->cartRepository->getOptionInfoByIds($optIds);
            if(count($optIds) != count($optionInfos)) { // 옵션 정보와 실제 옵션테이블의 정보가 맞지 않음
                $option_id = '';
                $errorType = 'reload';

            }
            foreach($optionInfos as $val) {
                if($val->amt<1) {
                    $option_id = $val->id;
                    $errorType = 'soldout';
                    break;
                } else if($val->amt < $optDatas[$val->id]) { // 구매 수량이 재고수량보다 많을때
                    $option_id = $val->id;
                    $errorType = 'overBuy';
                }

            }

            if($errorType) {
                return ['status'=>'message','data'=>['type'=>$errorType,'option_id'=>$option_id]];
            }
            $fieldset['ctype'] = $ctype;
            $fieldset['user_code'] = ($this->isLoginInfo && $this->isLoginInfo->id)?$this->isLoginInfo->id:$this->noMemberId;
            $fieldset['pid'] = $pid;
            if($productInfo->optionType=='single') { // 단독형 옵션일때
                $cartInfo = $this->cartRepository->getCartInfoByPid($pid);
                if($cartInfo && $cartInfo->id) {
                    // 기존 장바구니 정보 삭제
                    $this->cartRepository->deleteCartByPid($pid);
                }
                $fieldset['singleOptionInfos'] = $requestParams['option_info'];
                if(count($requiredOptionIds)>0) { // 필수옵션 정보가 있을경우
                    $fieldset['camt'] = $requiredOptionCamt;
                    $fieldset['option_id'] = $requiredOptionId; //옵션고유키
                    $data = $this->cartRepository->insertCart($fieldset);
                } else {
                    $fieldset['camt'] = $requestParams['camt'];
                    $data = $this->cartRepository->insertCart($fieldset);
                }

            } else {
                foreach($optDatas as $option_id=>$camt) {
                    $cartInfo = $this->cartRepository->getCartInfoByPidWithOptionId($pid,$option_id);
                    if($cartInfo && $cartInfo->id) {
                        $this->cartRepository->deleteCart($cartInfo->id);
                    }
                    $fieldset['camt'] = $camt; // 주문수량
                    $fieldset['option_id'] = $option_id; //옵션고유키
                    $data = $this->cartRepository->insertCart($fieldset);
                }
            }
        } else {
             // 기존에 데이타가 저장되었는지 체크
            $cartInfo = $this->cartRepository->getCartInfoByPid($pid);
            if($cartInfo && $cartInfo->id) {
                // 기존 장바구니 정보 삭제
                $this->cartRepository->deleteCartByPid($pid);
            }
            $fieldset['pid'] = $pid;
            $fieldset['camt'] = $requestParams['camt'];
            $fieldset['ctype'] = $ctype;
            $fieldset['user_code'] = ($this->isLoginInfo && $this->isLoginInfo->id)?$this->isLoginInfo->id:$this->noMemberId;
            $data = $this->cartRepository->insertCart($fieldset);

         }
         return ['status'=>'success','data'=>$data];
    }

    // 장바구니 목록
    public function getCartList(Request $request) {

        $user_id = (!empty($this->isLoginInfo))?$this->isLoginInfo->id:$this->noMemberId;
        $cartIds = (!empty($request->input('cart_ids')))?explode(',',$request->input('cart_ids')):[];
        $cartList = $this->cartRepository->getUserCartListByIds($user_id,$cartIds,'base');
        return $this->commonService->getCartParseDatas($cartList);


    }

    // 장바구니 구매수량 변경
    public function updateCartCamt(Request $request) {
        $cartInfo = $this->cartRepository->getCartInfo($request->input('id'));
        if($cartInfo->id) {
            if($cartInfo->option_id) { // 조합형 옵션 또는 옵션 단독형 옵션일때 옵션 필수가 있을경우
                $optionInfo = $this->cartRepository->getOptionInfo($cartInfo->option_id);
                if(!$optionInfo) { // 사용여부 재설정으로 인한 옵션값없을경우
                    return ['status'=>'message','data'=>'reload'];
                }
                if($optionInfo->amt < $request->input('camt')) { // 구매수량이 판매가능 재고량 보다 많을때
                    return ['status'=>'message','data'=>'overAmt'];
                }

            } else {
                $productInfo = $this->productRepository->getProductInfo($cartInfo->pid);
                if($productInfo->gamt < $request->input('camt')) { // 구매수량이 판매가능 재고량 보다 많을때
                    return ['status'=>'message','data'=>'overAmt'];
                }
            }
            $params['camt'] = $request->input('camt');
            $this->cartRepository->updateCart($cartInfo->id,$params);
            return ['status'=>'success','data'=>''];

        } else {
            return ['status'=>'error','data'=>''];

        }
    }

    // 장바구니 삭제
    public function deleteCart(Request $request) {
        if($request->input('type') == 'all') {
            $user_code = ($this->isLoginInfo && $this->isLoginInfo->id)?$this->isLoginInfo->id:$this->noMemberId;
            $this->cartRepository->deleteUserCartAll($user_code);
            return ['status'=>'success','data'=>''];
        } else {
            $this->cartRepository->deleteCart($request->input('id'));
            return ['status'=>'success','data'=>''];
        }

    }

}
