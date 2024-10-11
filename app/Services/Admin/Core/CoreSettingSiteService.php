<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeSettingSiteRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeBoardRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductCategoryRepository;

use Illuminate\Http\Request;

class CoreSettingSiteService {

    protected $siteRepository;
    protected $boardRepository;
    protected $categoryRepository;

    public function __construct(CustomizeSettingSiteRepository $siteRepository,
                                CustomizeBoardRepository $boardRepository,
                                CustomizeProductCategoryRepository $categoryRepository) {

        $this->siteRepository = $siteRepository;
        $this->boardRepository  =  $boardRepository;
        $this->categoryRepository = $categoryRepository;

    }

    /**
    *@ 업체정보
    **/
    public function getCompanyInfo() {
        $dataInfo = $this->siteRepository->getSiteInfoByField('company');
        $data['ocday'] = config('order.ocday');
        if(!empty($dataInfo)) {
            if($dataInfo->company) {
                $company = json_decode($dataInfo->company);
                $data['company'] = $company;
                if(!empty($company->ocday)) {
                    $data['ocday'] = $company->ocday;
                }
            }
        }


        return ['status'=>'success','data'=>$data];
    }


    public function getSiteEnv() {
        $data = $this->siteRepository->getSiteInfoByField('siteEnv');
        $siteEnv = 'developer';
        if(!empty($data)) {
            if($data->siteEnv) {
                $data->siteEnv = json_decode($data->siteEnv);
                $siteEnv = $data->siteEnv->siteEnv;
            }
        }
        return $siteEnv;

    }

    /**
    *@ 회원가입 정보
    **/
    public function getMemberInfo() {
        $data = $this->siteRepository->getSiteInfoByField('member');
        if(!empty($data)) {
            if($data->member)$data->member = json_decode($data->member);
        }
        return ['status'=>'success','data'=>$data];
    }

    /**
    *@ 약관 정보
    **/
    public function getAgreeInfo() {
        $data = $this->siteRepository->getSiteInfoByField('agrees');
        if(!empty($data)) {
            if($data->agrees)$data->agrees = json_decode($data->agrees);
        }
        return ['status'=>'success','data'=>$data];
    }

    /**
    *@ 이미지크기 정보
    **/
    public function getImageInfo() {
        $data = $this->siteRepository->getSiteInfoByField('images');
        if(!empty($data)) {
            if($data->images)$data->images = json_decode($data->images);
        }
        return ['status'=>'success','data'=>$data];
    }

    /**
    *@  포인트 설정 정보
    **/
    public function getPointInfo() {
        $data = $this->siteRepository->getSiteInfoByField('points');
        if(!empty($data)) {
            if($data->points)$data->points = json_decode($data->points);
        }
        return ['status'=>'success','data'=>$data];
    }
    /**
    *@  로고 설정 정보
    **/
    public function getLogoInfo() {
        $data = $this->siteRepository->getSiteInfoByField('logo');
        if(!empty($data)) {
            if($data->logo)$data->logo = json_decode($data->logo);
        }
        return ['status'=>'success','data'=>$data];
    }
    /**
    *@  메뉴 설정 정보
    **/
    public function getMenuInfo() {
        $menuInfo = $this->siteRepository->getSiteInfoByField('menu');
        $data['menuList'] = [];
        if(!empty($menuInfo)) {
            if($menuInfo->menu) {
                $data['menuList'] = json_decode($menuInfo->menu);
            }
        }
        $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
        $data['boardList'] = $this->boardRepository->getBoardUseList();

        return ['status'=>'success','data'=>$data];
    }

    /**
    *@  메인 설정 정보
    **/
    public function getMainInfo() {
        $mainInfo = $this->siteRepository->getSiteInfoByField('mainPage');
        $data = [];
        if(!empty($mainInfo)) {
            if($mainInfo->mainPage) {
                $mainInfo->mainPage = json_decode($mainInfo->mainPage);
                $data['main'] = $mainInfo->mainPage;
            }
        }
        $data['categoryList'] = $this->categoryRepository->getCategoryUseList();
        $data['boardList'] = $this->boardRepository->getBoardUseList();
        $logoInfo = $this->siteRepository->getSiteInfoByField('logo');
        if(!empty($logoInfo)) {
            if($logoInfo->logo) {
                $data['logoInfo'] = json_decode($logoInfo->logo);
            }
        }
        return ['status'=>'success','data'=>$data];
    }

    /**
    *@  로고 수정/저장
    **/
    public function updateLogo(Request $request) {


        $logoImg = '';
        if($request->input('logoType') != 'text') {
            if($request->file('logoImg')){
                $uploadParams['type'] = ['image'];
                $uploadParams['resize'] = '';
                $imgName = uploadFile($request,'logoImg',$this->siteRepository->logoImgPath,$uploadParams);
                if(empty($imgName)) {
                    return ['status'=>'message','data'=>'wrongTopImg'];
                }
                $logoImg = $this->siteRepository->logoImgUrl.$imgName;
            }
        }
        if($request->input('logoPosition') == 'top') {
            $params['topLogoType'] = $request->input('logoType');
            if(!empty($request->input('logoText')))$params['topLogoText'] = $request->input('logoText');
            if($logoImg) {
                $params['topLogoImg'] = $logoImg;
            }
        } else {
            $params['bottomLogoType'] = $request->input('logoType');
            if(!empty($request->input('logoText')))$params['bottomLogoText'] = $request->input('logoText');
            if($logoImg) {
                $params['bottomLogoImg'] = $logoImg;
            }
        }

        return $this->updateSiteSetting($params,'logo');
    }

    /**
    *@  메인배너 수정/저장
    **/
    public function updateMainBanner(Request $request) {

        $bannerImgs = [];
        for($k=1; $k<=$request->input('bannerImgCnt'); $k++) {
            if(!empty($request->input('isBannerImg'.$k))) {
                $bannerImgs[] = $request->input('isBannerImg'.$k);

            } else if(!empty($request->file('bannerImg'.$k))) {
                $uploadParams['type'] = ['image'];
                $uploadParams['resize'] = '';
                $imgName = uploadFile($request,'bannerImg'.$k,$this->siteRepository->logoImgPath,$uploadParams);
                if(empty($imgName)) {
                    return ['status'=>'message','data'=>'wrongImg'];
                }
                $bannerImgs[] = $this->siteRepository->logoImgUrl.$imgName;
            }
        }
        $params['mainBanner']= ['imgs'=>$bannerImgs,'info'=>$request->input('bannerInfo'),'bannerDatas'=>$request->input('bannerDatas')];
        return $this->updateSiteSetting($params,'mainPage');
    }
    /**
    *@  설정정보 저장
    **/
    public function updateSiteSetting(array $params,string $field) {

        //unset($params['_token']);
        $info = $this->siteRepository->getSiteInfo();
        if($info && $info->id) { // 저장된 정보가 있을경우

            if($info->$field) {
                $isData = json_decode($info->$field);

                foreach($params as $key=>$val) {
                    $isData->$key = $val;
                }
                $updParams[$field] = json_encode($isData);

            } else {
                $updParams[$field] = json_encode($params);
            }
            $result = $this->siteRepository->updateSite($info->id,$updParams);

        } else {
            $insParams[$field] = json_encode($params);
            $result = $this->siteRepository->insertSite($insParams);
        }
        if($result) {
            return ['status'=>'success','data'=>$result];
        } else {
            return ['status'=>'fail','data'=>''];
        }
    }


    // pg 정보
    public function getPaymentCompanyInfo(Request $request) {

         $data['paymethods'] = config('order.paymethods');
         $siteInfo = $this->siteRepository->getSiteInfo();
         if($siteInfo && $siteInfo->order) {
            $data['info'] = json_decode($siteInfo->order);

         }

         return ['status'=>'success','data'=>$data];
    }
}
