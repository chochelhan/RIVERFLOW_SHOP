<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeOrderReviewRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductBrandRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeCommentRepository;

use Illuminate\Http\Request;

class CoreOrderReviewService {

    protected $orderReviewRepository;
    protected $productRepository;
    protected $productCategoryRepository;
    protected $productBrandRepository;
    protected $commentRepository;

    public function __construct(CustomizeOrderReviewRepository $orderReviewRepository,
                                CustomizeProductCategoryRepository $productCategoryRepository,
                                CustomizeProductBrandRepository $productBrandRepository,
                                CustomizeProductRepository $productRepository,
                                CustomizeCommentRepository $commentRepository) {

        $this->orderReviewRepository = $orderReviewRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productBrandRepository = $productBrandRepository;
        $this->productRepository = $productRepository;
        $this->commentRepository = $commentRepository;
    }

    // 상품(구매) 후기 목록( 모든 데이타를 가져옴)
    public function getReviewList(Request $params) {

        $requestParams = $params->all();
        $data['categoryList'] = $this->productCategoryRepository->getCategoryUseList();
        $data['brandList'] = $this->productBrandRepository->getBrandUseList();

        $limit = ($params->input('limit'))?$params->input('limit'):20;
        $data['reviewList'] = $this->orderReviewRepository->getReviewDataList($requestParams,$limit);

         return $data;
    }
    // 상품(구매) 후기 데이타 목록
    public function getReviewDataList(Request $params) {
        $requestParams = $params->all();
        $limit = ($params->input('limit'))?$params->input('limit'):20;
        $data = $this->orderReviewRepository->getReviewDataList($requestParams,$limit);
        return $data;
    }

    // 상품 후기 상세
    public function getReviewInfo(Request $params) {
        $data['info'] = $this->orderReviewRepository->getOrderReivewInfo($params->input('id'));
        $data['productInfo'] = $this->productRepository->getProductInfo($data['info']->pid);
        $data['commentList'] = $this->commentRepository->getCommentList($data['info']->id,'orderReview');

        return $data;
    }

    // 상품 후기 블라인드 처리
    public function blindReview(Request $params) {

        $result = $this->orderReviewRepository->blindReviews($params->input('ids'),$params->input('cmd'));
        if($result) {
            $status = 'success';
        } else {
            $status = 'fail';
        }
        return ['status'=>$status,'data'=>''];
    }

}
