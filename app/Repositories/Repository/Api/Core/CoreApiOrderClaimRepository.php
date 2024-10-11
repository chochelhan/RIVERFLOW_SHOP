<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseOrderClaimRepository;


class CoreApiOrderClaimRepository extends  BaseOrderClaimRepository {

    public function insertClaim(array $fieldset) {
        $insData = $this->orderClaim::create($fieldset);
        return $insData;
    }

    public function insertClaimProduct(array $fieldset) {
        $insData = $this->orderClaimProduct::create($fieldset);
        return $insData;
    }
    // 졵재유무 체크
    public function isClaimData(int $oid) {
        return $this->orderClaim::where('oid',$oid)->first();

    }

    // 수정
    public function updateClaim(int $id,array $fieldset) {
        return $this->orderClaim::find($id)->update($fieldset);
    }
}

