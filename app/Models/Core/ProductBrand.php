<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


class ProductBrand extends Model {

    public $table;
    protected $fillable = [
        'bname',
        'buse',
        'brank'
    ];

    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.productBrand');

        foreach($this->fillable as $key) {
            if($key == 'brank')continue;
            $this->useFields[$key] = $key;
        }
    }


}
