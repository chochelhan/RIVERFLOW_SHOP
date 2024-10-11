<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    public $table;

    protected $fillable = [
         'pname', // 상품명
         'pcode', // 상품코드
         'serviceType', // normal 배송상품 , service 서비스상품
         'adult', // 사용안함
         'keyword', // 키워드
         'brandId', //브랜드 고유키
         'category1', // 카테고리 1 (1,2,4) 형태로 1차,2차,3차 카테고리가 저장됨
         'category2', // 카테고리 2 (1,2,4) 형태로 1차,2차,3차 카테고리가 저장됨
         'category3', // 카테고리 3 (1,2,4) 형태로 1차,2차,3차 카테고리가 저장됨
         'optionUse', // yes 옵션사용 , no 사용안함
         'optionInfo', // 옵션정보 json
         'price', // 판매가
         'dcprice', // 즉시 할인가
         'gamt', // 재고
         'detailImgs', // 상세 이미지 json
         'listImg', // 목록 이미지 string
         'content', // 상세내용
         'addInfos', // 추가정보 내용 json
         'addInfoId', // 추가정보 고유키
         'description', // 간략설명
         'pInfoNoti', // 상품정보고시 json
         'dcontent',// 배송 상세내용
         'deliveryId', // 배송정보 (배송테이블 고유키)
         'deliveryGroup', // 묶음배송가능여부 yes(가능),no(불가능)
         'pstatus', // sale (판매), hidden(노출중지),soldout(품절)
         'platform', // pc,mw(모바일),ma(모바일앱)
         'periodStdate', //기간 지정시 판매기간 시작일
         'periodEndate', // 기간 지정시 판매기가 종료일
         'salePeriod', // every 상시 , period 판매기간 사용
         'pointType', // 구매시 적립금 적립 yes 사용, no 사용안함
         'point', // 구매시 적립금
         'pointSet', // 적립금 적립타입 fix(정액), rate(정률)
         'pointUse', // 구매시 적립금 사용가능 여부 yes(사용), no(사용안함)
         'optionType', // 옵션사용 선택시 옵션타입 single(단독형), multi(조합형)
         'wish',// 관심 갯수
         'relProducts', // 관련상품
         'relUse', // 관련상품 사용여부
         'relType',  //  관련상품 type (single,category,brand)
         'relDeny', // 제외대상 사용여부


    ];

    public $useFields = [];
    public $queryParams = [];

    public function __construct() {
        $this->table = config('tables.product');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'detailImgs':
                case 'listImg':
                case 'pcode':
                case 'wish':
                case 'gamt':
                    continue;
                break;
                default:
                    $this->useFields[$key] = $key;
                break;
             }
        }
    }
    public function setQueryParams($params) {
        $this->queryParams = $params;
    }
}
