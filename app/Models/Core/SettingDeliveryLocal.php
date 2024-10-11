<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


class SettingDeliveryLocal extends Model {

    public $table = 'setting_delivery_local';

    protected $fillable = [
        'name',
        'localData'
    ];
    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.settingDeliveryLocal');
        foreach($this->fillable as $key) {
            if($key == 'localData')continue;
            $this->useFields[$key] = $key;
        }
    }


}
