<?php

namespace App\Repositories\Repository\Api\Core;

use App\Models\Customize\CustomizeCart;
use App\Models\Customize\CustomizeProductOption;
use App\Models\Customize\CustomizeProductCategory;

class CoreApiCartRepository {

    protected $cart;
    protected $productOption;
    protected $productCategory;
    public $useFields;

    public function __construct(CustomizeCart $cart,
                                CustomizeProductOption $productOption,
                                CustomizeProductCategory $productCategory) {
        $this->cart = $cart;
        $this->productOption = $productOption;
        $this->productCategory = $productCategory;

        $this->useFields = $this->cart->useFields;
    }

    public function getCartInfo(int $id) {
        return $this->cart::find($id);
    }
    // pid 값으로 cart info
    public function getCartInfoByPid(int $pid) {
        return $this->cart::where('pid',$pid)->first();
    }
    // pid and option_id 값으로 cart infos
    public function getCartInfoByPidWithOptionId(int $pid,int $optionId) {
            return $this->cart::where('pid',$pid)->where('option_id',$optionId)->first();
    }

    public function insertCart(array $fieldset) {
        $insData = $this->cart::create($fieldset);
        return $insData;

    }
    public function updateCart(int $id,array $params) {
        $this->cart::find($id)->update($params);
    }
    // 장바구니 삭제 (by 상품 고유키)
    public function deleteCartByPid(int $pid) {
        $this->cart::where('pid',$pid)->delete();
    }
    // 장바구니 삭제
    public function deleteCart(int $id) {
        $this->cart::destroy($id);
    }
    // 장바구니 삭제 (회원이 가진 모든 장바구니)
    public function deleteUserCartAll(string $user_code) {
        $this->cart::where('user_code',$user_code)->delete();
    }

    /**
    *
    *@ 상품 옵션 테이블에서 값 가져오기 start ***************
    **/
    // ids 값으로 옵션가져오기
    public function getOptionInfoByIds(array $ids) {
        return $this->productOption::whereIn('id',$ids)->where('ouse','Y')->orderBy('id','desc')->get();

    }
    // 필수옵션 여부 체크
    public function getOptionInfoByPidWithRequired(int $pid) {
        return $this->productOption::where('pid',$pid)->where('ouse','Y')->where('orequired','Y')->get();

    }
    // id 값으로 옵션가져오기
    public function getOptionInfo(int $id) {
        return $this->productOption::where('id',$id)->where('ouse','Y')->first();

    }
    /**
    *
    *@ 상품 옵션 테이블에서 값 가져오기 end ***************
    **/


    /**
    *@ 상품 카테고리 이름 가져오기
    **/
    //ids 값으로 가져오기
    public function getCategoryNamesByIds(array $ids) {
        return $this->productCategory::select('cname')->whereIn('id',$ids)->orderBy('depth','asc')->get();

    }
    public function getUserCartListWithServiceById($user_code,$id,string $type) {

        $dTable = config('tables.settingDelivery'); // 배송정보 테이블
        $pTable = config('tables.product');

        $select =[
            $this->cart->table.'.*',
            ////// 상품정보 /////////
            $pTable.'.pname',
            $pTable.'.pcode',
            $pTable.'.category1', // 대표 카테고리
            $pTable.'.category2', // 카테고리
            $pTable.'.category3', // 카테고리
            $pTable.'.brandId', // 카테고리
            $pTable.'.listImg',
            $pTable.'.serviceType', // 서비스타입
            $pTable.'.pointType', // 구매시 적립금 적립 yes 사용, no 사용안함
            $pTable.'.point', // 구매시 적립금
            $pTable.'.pointSet', // 적립금 적립타입 fix(정액), rate(정률)
            $pTable.'.pointUse', // 구매시 적립금 사용가능 여부 yes(사용), no(사용안함)
            $pTable.'.optionType', // 옵션사용 선택시 옵션타입 single(단독형), multi(조합형)
            $pTable.'.optionUse', // 옵션사용 선택시 옵션타입 single(단독형), multi(조합형)
            $pTable.'.gamt', //재고
            $pTable.'.price', //가격
            $pTable.'.dcprice', //할인가
            $pTable.'.deliveryId', //배송비 고유키

        ];
        //$pTable.'.platform', // pc,mw(모바일),ma(모바일앱)
        $nowDate = date('Y-m-d');
        return $this->cart::where('user_code',$user_code)
                                ->select($select)
                                ->leftJoin($pTable,$pTable.'.id','=',$this->cart->table.'.pid')
                                ->where($this->cart->table.'.id',$id)
                                ->where($this->cart->table.'.ctype',$type)
                                ->where($pTable.'.pstatus','!=','hidden')
                                ->whereRaw("(".$pTable.".salePeriod='every' OR
                                 (".$pTable.".salePeriod='period' AND (".$pTable.".periodStdate<=?
                                  AND ".$pTable.".periodEndate>=?)))",[$nowDate,$nowDate])
                                ->orderBy($this->cart->table.'.id','desc')
                                ->get();


    }
    public function getUserCartListByIds($user_code,array $ids,string $type) {

        $dTable = config('tables.settingDelivery'); // 배송정보 테이블
        $pTable = config('tables.product');
        $select =[
            $this->cart->table.'.*',
            ////// 상품정보 /////////
            $pTable.'.pname',
            $pTable.'.pcode',
            $pTable.'.category1', // 대표 카테고리
            $pTable.'.category2', // 카테고리
            $pTable.'.category3', // 카테고리
            $pTable.'.brandId', // 카테고리
            $pTable.'.listImg',
            $pTable.'.serviceType', // 서비스타입
            $pTable.'.pointType', // 구매시 적립금 적립 yes 사용, no 사용안함
            $pTable.'.point', // 구매시 적립금
            $pTable.'.pointSet', // 적립금 적립타입 fix(정액), rate(정률)
            $pTable.'.pointUse', // 구매시 적립금 사용가능 여부 yes(사용), no(사용안함)
            $pTable.'.optionType', // 옵션사용 선택시 옵션타입 single(단독형), multi(조합형)
            $pTable.'.optionUse', // 옵션사용 선택시 옵션타입 single(단독형), multi(조합형)
            $pTable.'.gamt', //재고
            $pTable.'.price', //가격
            $pTable.'.dcprice', //할인가
            $pTable.'.deliveryId', //배송비 고유키

            /// 배송정보
            $dTable.'.dmethod', // auto(택배/등기/소포) , direct (직접배송)
            $dTable.'.dpriceType', // price(유료 고정배송비), half (조건부 무료) , free(무료)
            $dTable.'.oprice', // 조건부사용일대 기준금액 (이상이면 무료)
            $dTable.'.fprice', // 고정배송비
            $dTable.'.mprice', // 조건부사용일대 oprice 미안일대 배송비
            $dTable.'.localId', // 지역별 배송비 사용일대 지역별 배송비 고유키
            $dTable.'.localUse', // yes 지역별 배송비 사용 ,no 사용안함
                     //'backPrice', // 환불교환 배송비
                     //'backAddr', // 환불 교환 주소지
        ];
        $device = isMobile();
        $queryString = 'find_in_set(?,'.$pTable.'.platform)>0';
        $nowDate = date('Y-m-d');
        $params['type'] = $type;
        $params['ids'] = $ids;

        $this->cart->setQueryParams($params);
        return $this->cart::where('user_code',$user_code)
                                ->select($select)
                                ->leftJoin($pTable,$pTable.'.id','=',$this->cart->table.'.pid')
                                ->leftJoin($dTable,$dTable.'.id','=',$pTable.'.deliveryId')
                                ->where(function($query) {
                                    $queryParams = $this->cart->queryParams;
                                    $ids = $queryParams['ids'];
                                    if($queryParams['type']=='temp') {
                                        $id = $ids[0];
                                        $query->where($this->cart->table.'.id',$id);
                                    } else {
                                        $pTable = config('tables.product');
                                        if(count($ids)>0) {
                                            $query->whereIn($this->cart->table.'.id',$ids);
                                        }
                                        $query->where($pTable.'.deliveryGroup','yes'); // 묶음배송 가능한것만 나옴
                                    }
                                })
                                 ->whereRaw($queryString,$device)
                                ->where($this->cart->table.'.ctype',$type)
                                ->where($pTable.'.pstatus','!=','hidden')
                                ->whereRaw("(".$pTable.".salePeriod='every' OR
                                 (".$pTable.".salePeriod='period' AND (".$pTable.".periodStdate<=?
                                  AND ".$pTable.".periodEndate>=?)))",[$nowDate,$nowDate])
                                ->orderBy($this->cart->table.'.id','desc')
                                ->get();


    }

    // 결제후에 장바구니 정보 삭제
    public function deleteUserCartListByIds(string $user_code,array $ids,string $type) {

        if($type =='temp') {
            $this->cart::where('user_code',$user_code)
                                        ->where($this->cart->table.'.ctype',$type)
                                        ->delete();

        } else {
            $this->cart::where('user_code',$user_code)
                                    ->when($ids,function($query,$ids) {
                                        return $query->whereIn($this->cart->table.'.id',$ids);
                                    })
                                    ->where($this->cart->table.'.ctype',$type)
                                    ->delete();

        }
    }
}
