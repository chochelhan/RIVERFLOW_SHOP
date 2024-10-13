<?php

namespace App\Http\Controllers\Admin\Core;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Customize\CustomizeSettingSiteService;
use App\Services\Admin\Customize\CustomizeSettingDeliveryService;
use App\Services\Admin\Customize\CustomizeSettingDeliveryLocalService;
use Illuminate\Http\response;

class CoreSettingController extends Controller
{
    protected $siteService;
    protected $deliveryService;
    protected $deliveryLocalService;

    public function __construct(CustomizeSettingSiteService $siteService,
                                CustomizeSettingDeliveryService $deliveryService,
                                CustomizeSettingDeliveryLocalService $deliveryLocalService) {

        $this->siteService = $siteService;
        $this->deliveryService = $deliveryService;
        $this->deliveryLocalService = $deliveryLocalService;

    }

    /**
    *@ 배송비 템플릿
    **/
    /*** 배송비 템플릿 저장 ***/
    public function insertDelivery(Request $request) {
        $data = $this->deliveryService->insertDelivery($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /*** 배송비 템플릿 수정 ***/
    public function updateDelivery(Request $request) {
        $data = $this->deliveryService->updateDelivery($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 배송비 템플릿 삭제 **/
    public function deleteDelivery(Request $request) {
        $data = $this->deliveryService->deleteDelivery($request);
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 배송비 템플릿 순서 변경 **/
    public function sequenceDelivery(Request $request) {
        $data = $this->deliveryService->sequenceDelivery($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 묶음 배송비 산출방식 **/
    public function updateDeliveryGroupType(Request $request) {

        $params['groupType'] = $request->input('groupType');

        $data = $this->siteService->updateSiteSetting($params,'delivery');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }


    /**
    *@ 지역별 추가 배송비
    **/
    /*** 지역별 추가 배송비 저장 ***/
    public function insertDeliveryLocal(Request $request) {
        $data = $this->deliveryLocalService->insertDeliveryLocal($request);

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);
    }

    /*** 지역별 추가 배송비 수정 ***/
    public function updateDeliveryLocal(Request $request) {
        $data = $this->deliveryLocalService->updateDeliveryLocal($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 지역별 추가 배송비 삭제 **/
    public function deleteDeliveryLocal(Request $request) {
       $data = $this->deliveryLocalService->deleteDeliveryLocal($request);
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /*** 배송업체설정 **/
    public function updateDeliveryCompany(Request $request) {
       $params['duseCompany'] = json_decode($request->input('duseCompany'));
       $data = $this->siteService->updateSiteSetting($params,'delivery');
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }


    /*** PG설정 **/
    public function updatePaymentCompany(Request $request) {
       if(!$request->has('gtype')) {
            return restResponse(['status'=>'emptyFiled','data'=>'']);
       }
       if($request->input('gtype') == 'pg') {
            if(!$request->has(['pg'])) {
                   return restResponse(['status'=>'emptyFiled','data'=>'']);
            }
            $params['pgSetting'] = [
                'pg'=>$request->input('pg'),
                'pgApiKey'=> $request->input('pgApiKey'),
                'pgApiSecret' => $request->input('pgApiSecret'),
                'pgCode' => $request->input('pgCode')
                ];

       } else {

            $params['paymethods'] = json_decode($request->input('usePaymethodList'));
            $params['bankSetting'] = [
                'bankName'=>$request->input('bankName'),
                'bankAccount'=>$request->input('bankAccount'),
                'bankOwner'=>$request->input('bankOwner'),
                'expireDay'=>$request->input('expireDay'),
            ];

       }
       $data = $this->siteService->updateSiteSetting($params,'order');
       return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@ 업체정보 설정
    **/
    /*** 업체정보 등록/수정 **/
    public function updateCompany(Request $request) {

        $data = $this->siteService->updateSiteSetting($request->all(),'company');

        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  회원가입정보 설정
    **/
    /*** 회원가입정보 등록/수정 **/
    public function updateMember(Request $request) {

        $data = $this->siteService->updateSiteSetting($request->all(),'member');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  약관정보 설정
    **/
    /*** 약관정보 등록/수정 **/
    public function updateAgree(Request $request) {

        $params[$request->input('agreeType')] = $request->input('data');
        $data = $this->siteService->updateSiteSetting($params,'agrees');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  이미지 크기 정보 설정
    **/
    /*** 이미지 크기  등록/수정 **/
    public function updateImage(Request $request) {

        $params[$request->input('imageType')] = $request->input('data');
        $data = $this->siteService->updateSiteSetting($params,'images');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  적립금 정보 설정
    **/
    /*** 적립금 설정정보 등록/수정 **/
    public function updatePoint(Request $request) {

        $data = $this->siteService->updateSiteSetting($request->all(),'points');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }
    /**
    *@  메뉴 정보 설정
    **/
    /*** 메뉴 등록/수정 **/
    public function updateMenu(Request $request) {

        $data = $this->siteService->updateSiteSetting($request->all(),'menu');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  로고 정보 설정
    **/
    /*** 로고 등록/수정 **/
    public function updateLogo(Request $request) {

        $data = $this->siteService->updateLogo($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }

    /**
    *@  메인페이지 정보 설정
    **/
    /*** 배너 등록/수정 **/
    public function updateMainBanner(Request $request) {
        $data = $this->siteService->updateMainBanner($request);
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }
    /*** 전시항목 등록/수정 **/
    public function updateMainDisplay(Request $request) {
        $data = $this->siteService->updateSiteSetting($request->all(),'mainPage');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }


    /*** 실행환경 변경 **/
    public function updateSiteEnv(Request $request) {
        $data = $this->siteService->updateSiteSetting($request->all(),'siteEnv');
        return response()->json(['status' => $data['status'], 'data' => $data['data']]);

    }


}
