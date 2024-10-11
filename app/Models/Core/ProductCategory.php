<?php


namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


class ProductCategory extends Model {

    public $table;

    protected $fillable = [
        'pcode',
        'code',
        'cname',
        'cuse',
        'depth',
        'crank'
    ];

    public $useFields = [];
    public function __construct() {
        $this->table = config('tables.productCategory');

        foreach($this->fillable as $key) {
            if($key == 'crank')continue;
            $this->useFields[$key] = $key;
        }
    }


}
