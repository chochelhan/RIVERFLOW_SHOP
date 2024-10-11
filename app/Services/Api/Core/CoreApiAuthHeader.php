<?php
namespace App\Services\Api\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
*
**/
class CoreApiAuthHeader
{
    protected $isLoginInfo;
    protected $noMemberId;
    protected $siteInfos = [];
    public function __construct(Request $request) {


        if(!empty($request->bearerToken())) {
            $this->isLoginInfo = Auth('jwt')->user();


        } else {
            if($request->session()->missing('noMemberId')) {
                $noMemberId = mt_rand(10000,99999).'_'.mt_rand(10000,99999);
                $request->session()->put('noMemberId',$noMemberId);
            }
            $this->noMemberId = $request->session()->get('noMemberId');


        }
        $siteInfos = \App\Models\Customize\CustomizeSettingSite::first();
        $this->siteInfos['agrees'] = ($siteInfos->agrees)?json_decode($siteInfos->agrees):'';
        $this->siteInfos['company']  = ($siteInfos->company)?json_decode($siteInfos->company):'';
        //$this->siteInfos['coupons'] = ($siteInfos->coupons)?json_decode($siteInfos->coupons):'';
        $this->siteInfos['delivery'] = ($siteInfos->delivery)?json_decode($siteInfos->delivery):'';
        $this->siteInfos['images'] = ($siteInfos->images)?json_decode($siteInfos->images):'';
        $this->siteInfos['member'] = ($siteInfos->member)?json_decode($siteInfos->member):'';
        $this->siteInfos['points'] = ($siteInfos->points)?json_decode($siteInfos->points):'';
        $this->siteInfos['logo'] = ($siteInfos->logo)?json_decode($siteInfos->logo):'';
        $this->siteInfos['menu'] = ($siteInfos->menu)?json_decode($siteInfos->menu):'';
        $this->siteInfos['order'] = ($siteInfos->order)?json_decode($siteInfos->order):'';
        $this->siteInfos['mainPage'] = ($siteInfos->mainPage)?json_decode($siteInfos->mainPage):'';
        $this->siteInfos['siteEnv'] = ($siteInfos->siteEnv)?json_decode($siteInfos->siteEnv):'';
        
    }

}
