<?php

namespace App\Repositories\Interface;

interface SettingSiteRepositoryInterface {

    public function getSiteInfo();
    public function getSiteInfoByField(string $field);


}
