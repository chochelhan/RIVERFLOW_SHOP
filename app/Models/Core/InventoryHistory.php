<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    public $table;

    protected $fillable = [
        'ivt_id', // 재고고유키
        'type', // 변경타입 (up,down,disable,sale,return)
        'content',   // 변경 내용
    ];

    public function __construct() {
        $this->table = config('tables.inventoryHistory');

    }



}
