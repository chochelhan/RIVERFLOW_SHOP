<?php

namespace App\Repositories\Repository\Admin\Core;
use App\Repositories\Repository\Base\BaseSettingSiteRepository;

class CoreSettingSiteRepository extends BaseSettingSiteRepository {

    //등록
    public function insertSite(array $params) {
        $insData = $this->settingSite::create($params);
        return $insData;
    }

    //수정
    public function updateSite(int $id,array $params) {

        $updData = $this->settingSite::find($id)->update($params);
        return ($updData)?$id:'';
    }

}

