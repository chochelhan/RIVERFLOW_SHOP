<?php

namespace App\Repositories\Repository\Api\Core;

use App\Repositories\Repository\Base\BaseMemberLevelRepository;


class CoreApiMemberLevelRepository extends  BaseMemberLevelRepository {


   public function getBaseLevelInfo() {
        return $this->memberLevel::where('gbase','yes')->first();

   }
   
   public function getLevelInfo(int $id) {
        return $this->memberLevel::find($id);

   }

}

