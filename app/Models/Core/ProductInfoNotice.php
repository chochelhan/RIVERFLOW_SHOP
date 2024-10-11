<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


class ProductInfoNotice extends Model {

    public $table;
    protected $fillable = [
        'pid',
        'pname',
        'code',
        'datas',
    ];


    public function __construct() {
        $this->table = config('tables.productInfoNotice');

    }


}
