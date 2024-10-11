<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class BoardArticle  extends Model {

    public $table;
    protected $fillable = [
        'bid',
        'btype',
        'subject',
        'category',
        'content',
        'secret',
        'img',
        'hit',
        'commentCnt',
        'user_id',
        'user_name',
        'user_pass',

    ];
    public $useFields = [];

    public function __construct() {
        $this->table = config('tables.boardArticle');
        foreach($this->fillable as $key) {

            switch($key) {
                case  'commentCnt':
                case  'user_id':
                case  'user_pass':
                case  'img':
                case  'hit':
                    continue;
                break;
                default:
                    $this->useFields[$key] = $key;
                break;
            }

        }
    }


}
