<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeSettingDeliveryService;
use App\Services\Admin\Customize\CustomizeSettingDeliveryLocalService;
use App\Services\Admin\Customize\CustomizeSettingSiteService;
use App\Services\Admin\Customize\CustomizeAdminMainService;

class CoreSettingViewController extends Controller
{
    protected $siteService;
    protected $deliveryService;
    protected $deliveryLocalService;
    protected $adminMainService;

    public function __construct(CustomizeSettingDeliveryService $deliveryService,
                                CustomizeSettingDeliveryLocalService $deliveryLocalService,
                                CustomizeSettingSiteService $siteService,
                                CustomizeAdminMainService $adminMainService) {

        $this->siteService = $siteService;
        $this->deliveryService = $deliveryService;
        $this->deliveryLocalService = $deliveryLocalService;
        $this->adminMainService = $adminMainService;

    }

    /**
    *  사이트 전체정보
    **/
    public function getSiteTotalInfo() {
        $data = $this->adminMainService->getMainInfo();
        return restResponse(['status'=>'success','data'=>$data]);
    }



    /**
    * 배송지 템플릿 전체 목록
    **/
    public function getDeliveryList() {
        $list = $this->deliveryService->getDeliveryList();
        return restResponse(['status'=>'success','data'=>$list]);
    }

    /**
    * 지역별 추가 배송지 정보
    **/
    public function getDeliveryLocalInfo(request $request) {
        $data = $this->deliveryLocalService->getDeliveryLocalInfo($request);

        return restResponse($data);
    }


    /**
    * 배송업체 정보
    **/
    public function getDeliveryCompanyInfo(request $request) {
        $data = $this->deliveryService->getDeliveryCompanyInfo($request);

        return restResponse($data);
    }

    /**
    * PG 정보
    **/
    public function getPaymentCompanyInfo(request $request) {
        $data = $this->siteService->getPaymentCompanyInfo($request);

        return restResponse($data);
    }

    /**
    * 업체정보
    **/
    public function getCompany() {
        $data = $this->siteService->getCompanyInfo();

        return restResponse($data);
    }

    /**
    * 회원가입정보
    **/
    public function getMember() {
        $data = $this->siteService->getMemberInfo();

        return restResponse($data);
    }

    /**
    * 약관정보
    **/
    public function getAgree() {
        $data = $this->siteService->getAgreeInfo();

        return restResponse($data);
    }
    /**
    * 이미지정보
    **/
    public function getImage() {
        $data = $this->siteService->getImageInfo();

        return restResponse($data);
    }

    /**
    * 포인트 설정정보
    **/
    public function getPoint() {
        $data = $this->siteService->getPointInfo();

        return restResponse($data);
    }

    /**
    * 메뉴정보
    **/
    public function getMenu() {
        $data = $this->siteService->getMenuInfo();

        return restResponse($data);
    }
    /**
    * 로고정보
    **/
    public function getLogo() {
        $data = $this->siteService->getLogoInfo();

        return restResponse($data);
    }

    /**
    * 메인페이지정보
    **/
    public function getMain() {
        $data = $this->siteService->getMainInfo();

        return restResponse($data);
    }

    public function getServerPath() {
        $data = dirname($_SERVER['DOCUMENT_ROOT']);

        return restResponse(['status'=>'success','data'=>$data]);
    }

}
