<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SmsEmail  extends Model {

    public $table;
    protected $fillable = [
        'gtype', //sms,email
        'gid', // join,order .....
        'content', // json data
        'guse', // yse,no
    ];
    public function __construct() {
        $this->table = config('tables.smsEmailSetting');
    }


}