<?php

namespace App\Repositories\Repository\Base;

use App\Models\Customize\CustomizeProduct;
use App\Models\Customize\CustomizeOrderReview;

class BaseOrderReviewRepository  {

    protected $orderReview;
    public $useFields;

    public $filePath = 'public/board';
    public $imgUrl = '/boardImages/';


    public function __construct(CustomizeOrderReview $orderReview) {
        $this->orderReview = $orderReview;
        $this->useFields = $this->orderReview->useFields;
    }

    public function getOrderReivewInfo(int $id) {
        $memTable = config('tables.users');
        return $this->orderReview::where($this->orderReview->table.'.id',$id)->select($this->orderReview->table.'.*',$memTable.'.name')
                                            ->leftJoin($memTable,$memTable.'.id','=',$this->orderReview->table.'.user_id')->first();
    }


}

