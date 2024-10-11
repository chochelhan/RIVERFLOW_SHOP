<?php
namespace App\Repositories\Repository\Base;


use App\Models\Customize\CustomizeProduct;
use App\Models\Customize\CustomizeProductOption;

use App\Repositories\Interface\ProductRepositoryInterface;

class BaseProductRepository implements ProductRepositoryInterface {

    protected $product;
    protected $productOption;

    public $useFields;
    public $optionUseFields;
    public $filePath = 'public/product';
    public $detailImgSize = ['width'=>612,'height'=>612];
    public $listImgSize = ['width'=>276,'height'=>270];
    public $detailImgPath = 'public/productDetail';
    public $imgUrl = '/productImages/';
    public $editorImgUrl =  '/productDetailImages/';

    //'boardImages') => storage_path('app/public/board'),
    //'bannerImages') => storage_path('app/public/banner'),


    public function __construct(CustomizeProduct $product,CustomizeProductOption $productOption) {
        $this->product = $product;
        $this->productOption = $productOption;

        $this->useFields  = $this->product->useFields;
        $this->optionUseFields  = $this->productOption->useFields;


    }
    // 상품 정보
    public function getProductInfo(int $id) {
        return $this->product::find($id);
    }

    public function getOptionInfoByPid(int $pid) {
        return $this->productOption::where('pid',$pid)->get();

    }
    //ids 값으로 옵션가져오기
    public function getOptionInfoByIds(array $ids) {
        return $this->productOption::whereIn('id',$ids)->orderBy('id','desc')->get();

    }


    // ids 값으로 상품목록 가져오기 (판매가능한 상품만)
    public function getProductListWithSaleByIds(array $ids) {
        return $this->product::whereIn('id',$ids)
                              ->where('pstatus','sale')
                              ->where(function($query) {
                                    $nowDate = date('Y-m-d');
                                    $query->where('salePeriod','every');
                                    $query->orWhere('salePeriod','period')
                                        ->where('periodStdate','<=',$nowDate.' 00:00:00')
                                        ->where('periodEndate','>=',$nowDate.' 23:59:59');
                              })
                              ->orderBy('id','desc')->get();
    }



}

