<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;


class ProductAddInfo extends Model {

    public $table;
    protected $fillable = [
        'bname',
        'buse',
        'brank',
        'itemList'
    ];

    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.productAddInfo');

        foreach($this->fillable as $key) {
            switch($key) {
                case 'brank':
                case 'itemList':
                    continue;
                    break;
                default:
                    $this->useFields[$key] = $key;
                    break;
            }
        }
    }

}
