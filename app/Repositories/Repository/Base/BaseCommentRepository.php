<?php

namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizeComment;

class BaseCommentRepository  {

    protected $comment;
    public $useFields;

    public function __construct(CustomizeComment $comment) {
        $this->comment = $comment;
        $this->useFields = $this->comment->useFields;



    }


}

