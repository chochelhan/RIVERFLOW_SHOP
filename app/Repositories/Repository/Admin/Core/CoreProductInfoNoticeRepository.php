<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Models\Customize\CustomizeProductInfoNotice;

class CoreProductInfoNoticeRepository {


    protected $pInfoNotice;

    public function __construct(CustomizeProductInfoNotice $pInfoNotice) {
        $this->pInfoNotice = $pInfoNotice;

    }
    // 정보 리스트
    public function getProductInfoNoticeList() {
        $results = $this->pInfoNotice::orderBy('id','DESC')->get();
        return $results;
    }
    //등록/수정
    public function updateProductInfoNotice(array $fieldsets) {
        $row = $this->pInfoNotice::where('pid',$fieldsets['pid'])->first();
        if($row && $row->id) {
            $params['code'] = $fieldsets['code'];
            $params['datas'] = $fieldsets['datas'];
            $this->pInfoNotice::find($row->id)->update($params);
        } else {
            $this->pInfoNotice::create($fieldsets);
        }


    }

    //삭제
    public function deleteProductInfoNotice(int $id) {
        $delData = $this->pInfoNotice::destroy($id);
        return ($delData)?$id:'';
    }
    // pid 로 삭제
    public function deleteProductInfoNoticeByPid(int $pid) {
        $this->pInfoNotice::where('pid',$pid)->delete();
    }

}

