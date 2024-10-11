<?php

namespace App\Services\Api\Core;

use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductCategoryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiBoardRepository;

use App\Services\Api\Core\CoreApiAuthHeader;
use Illuminate\Http\Request;

class CoreApiSearchService extends CoreApiAuthHeader {

    protected $productRepository;
    protected $categoryRepository;
    protected $boardRepository;

    public function __construct(Request $request,
                                CustomizeApiProductCategoryRepository $categoryRepository,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiBoardRepository $boardRepository) {
        parent::__construct($request);

        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->boardRepository = $boardRepository;

    }

    public function getCategoryList() {
        $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
        return ['status'=>'success','data'=>$data];
    }

    public function searchData(string $keyword) {

        $params['searchKeyword'] = $keyword;
        if(!empty($this->isLoginInfo) && !empty($this->isLoginInfo->id)) {
            $params['user_id'] = $this->isLoginInfo->id;
        }

        $data['productList'] = $this->productRepository->getProductList($params,1000);

        $boardParams['limit'] = 1000;
        $boardParams['getType'] = 'all';
        $boardParams['keyword'] = $keyword;
        $boardParams['keywordCmd'] = 'subject';

        $articleList = $this->boardRepository->getArticleList($boardParams);
        $articleListByBid = [];
        foreach($articleList as $key=>$article) {
            $articleListByBid[$article->bid][] = $article;
        }
        $boardList = $this->boardRepository->getBoardUseList();
        foreach($boardList as $key=>$board) {
            if(!empty($articleListByBid[$board->id])) {
                $data['boards'][$key]['info'] = $board;
                $data['boards'][$key]['list'] = $articleListByBid[$board->id];

            }
        }
        $data['imageset'] = $this->siteInfos['images'];
        $data['memberset'] = $this->siteInfos['member'];
        
        return ['status'=>'success','data'=>$data];
    }

}
