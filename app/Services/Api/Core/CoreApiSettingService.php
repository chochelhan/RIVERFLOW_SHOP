<?php

namespace App\Services\Api\Core;
use App\Services\Api\Core\CoreApiAuthHeader;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductCategoryRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiProductBrandRepository;
use App\Repositories\Repository\Api\Customize\CustomizeApiBoardRepository;

use Illuminate\Http\Request;

class CoreApiSettingService extends CoreApiAuthHeader {

    protected $productRepository;
    protected $categoryRepository;
    protected $brandRepository;
    protected $boardRepository;

    public function __construct(Request $request,
                                CustomizeApiProductRepository $productRepository,
                                CustomizeApiProductCategoryRepository $categoryRepository,
                                CustomizeApiProductBrandRepository $brandRepository,
                                CustomizeApiBoardRepository $boardRepository) {

        parent::__construct($request);

        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        $this->boardRepository = $boardRepository;

    }

    // 기본정보
    public function getBase() {
        $data['menu'] = $this->siteInfos['menu'];
        $data['logo'] = $this->siteInfos['logo'];
        $data['company'] = $this->siteInfos['company'];

        $data['pointName'] = ($this->siteInfos['points'])?$this->siteInfos['points']->pointName:'';
        $data['pointUnit'] = ($this->siteInfos['points'])?$this->siteInfos['points']->pointUnit:'';

        if(!empty($this->siteInfos['mainPage']))$data['mainBanner'] = $this->siteInfos['mainPage']->mainBanner;
        return $data;

    }

    // 메인페이지정보
    public function getMain() {
        $mainPage = $this->siteInfos['mainPage'];
	    $data = [];
        if($mainPage) {
			if(!empty($mainPage->mainDisplay)) {
                $mainDisplayList = [];
                foreach($mainPage->mainDisplay as $displayData) {
                    if($displayData->duse!='yes')continue;
                    if($displayData->dtype=='product') {
                        $categorys = '';
                        if(!empty($displayData->tcategory)) {
                            $categorys = $displayData->fcategory.','.$displayData->scategory.','.$displayData->tcategory;
                        } else if(!empty($displayData->scategory)) {
                            $categorys = $displayData->fcategory.','.$displayData->scategory;
                        } else if(!empty($displayData->fcategory)) {
                            $categorys = $displayData->fcategory;
                        }
                        if($categorys) {
                            $params['category'] = $categorys;
                            $displayData->productList =  $this->productRepository->getProductList($params,$displayData->maxCnt);

                        } else $displayData->productList =  [];

                    } else if($displayData->dtype=='board') {
                        if($displayData->boardId) {
                            $boardParams['limit'] = $displayData->maxCnt;
                            $boardParams['bid'] = $displayData->boardId;
                            $boardInfo = $this->boardRepository->getBoardInfo($displayData->boardId);
							if($boardInfo!=null) {
								$displayData->btype = $boardInfo->btype;
								$displayData->articleList = $this->boardRepository->getArticleList($boardParams);
							} else {
								$displayData->articleList = [];
							}
                        } else $displayData->articleList = [];

                    }
                    $mainDisplayList[] = $displayData;
                }

                $data['mainDisplayList'] = $mainDisplayList;
                $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
                $data['brandList'] = $this->brandRepository->getBrandUseList();
            }
            if(!empty($mainPage->mainBanner)){
                $data['mainBanner'] = $mainPage->mainBanner;
            }
        }
        return $data;

    }
}
