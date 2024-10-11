<?php
namespace App\Repositories\Repository\Base;


use App\Models\Customize\CustomizeSettingSite;
use App\Repositories\Interface\SettingSiteRepositoryInterface;

class BaseSettingSiteRepository implements SettingSiteRepositoryInterface {

    protected $settingSite;
    public $logoImgPath = 'public/banner';
    public $logoImgUrl = '/bannerImages/';

    public function __construct(CustomizeSettingSite $settingSite) {
        $this->settingSite = $settingSite;
    }
    // 사이트 설정 정보
    public function getSiteInfo() {
        return $this->settingSite::first();
    }
    public function getSiteInfoByField(string $field) {
        return $this->settingSite::select($field)->first();
    }
}

