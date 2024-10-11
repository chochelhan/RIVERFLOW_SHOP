<?php

namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizeMemberLevel;

class BaseMemberLevelRepository {

    protected $memberLevel;
    public $useFields;

    public function __construct(CustomizeMemberLevel $memberLevel) {
        $this->memberLevel = $memberLevel;
        $this->useFields = $this->memberLevel->useFields;
    }


}

