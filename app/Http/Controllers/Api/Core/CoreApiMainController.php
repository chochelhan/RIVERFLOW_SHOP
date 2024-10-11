<?php

namespace App\Http\Controllers\Api\Core;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

/**
*
**/
class CoreApiMainController extends Controller
{

    public function getMain(Request $request) {
        $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
        $siteEnv = 'developer';
        if(!empty($siteInfos)) {
            $siteEnvData = ($siteInfos->siteEnv)?json_decode($siteInfos->siteEnv):'';
            if($siteEnvData && !empty($siteEnvData->siteEnv)) {
                $siteEnv = $siteEnvData->siteEnv;
            }
        }
        $protocol = 'http';
        if(!empty($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS']=='on')?'https':'http';
        }
        if($siteEnv == 'production' && $protocol=='http') {
            return redirect('https://'.$_SERVER['HTTP_HOST'].'/');
        } else {
            $request->session()->regenerate();
            $token = csrf_token();
            $metaTitle = '';
            $metaKeyword = '';
            $metaContent = '';
            if(!empty($siteInfos) && !empty($siteInfos->company)) {
                $siteMetaInfo = json_decode($siteInfos->company);
                if($siteMetaInfo) {
                    $metaTitle = $siteMetaInfo->siteName;
                    $metaKeyword = (!empty($siteMetaInfo->metaKeyword))?$siteMetaInfo->metaKeyword:'';
                    $metaContent = (!empty($siteMetaInfo->metaContent))?$siteMetaInfo->metaContent:'';
                }

            }
            $data = ['csrf_token'=>$token,
                    'metaTitle'=>$metaTitle,
                    'metaKeyword'=>$metaKeyword,
                    'metaContent'=>$metaContent];

            return view('index',$data);
        }
    }

    public function getAdminMain(Request $request) {
        $request->session()->regenerate();
        $token = csrf_token();

        $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
        $metaTitle = '';
        $metaKeyword = '';
        $metaContent = '';
        if(!empty($siteInfos) && !empty($siteInfos->company)) {
            $siteMetaInfo = json_decode($siteInfos->company);
            if($siteMetaInfo) {
                $metaTitle = $siteMetaInfo->siteName;
                $metaKeyword = (!empty($siteMetaInfo->metaKeyword))?$siteMetaInfo->metaKeyword:'';
                $metaContent = (!empty($siteMetaInfo->metaContent))?$siteMetaInfo->metaContent:'';
            }

        }
        $data = ['csrf_token'=>$token,
                'metaTitle'=>$metaTitle,
                'metaKeyword'=>$metaKeyword,
                'metaContent'=>$metaContent];

        return view('index',$data);


    }
}
