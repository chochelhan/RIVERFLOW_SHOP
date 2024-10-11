<?php

namespace App\Services\Admin\Core;

use App\Repositories\Repository\Admin\Customize\CustomizeMemberRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderClaimRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInventoryRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeProductInquireRepository;
use App\Repositories\Repository\Admin\Customize\CustomizeOrderReviewRepository;

use Illuminate\Http\Request;

class CoreAdminMainService {

    protected $memberRepository;
    protected $orderRepository;
    protected $claimRepository;
    protected $inquireRepository;
    protected $reivewRepository;
    protected $productRepository;

    public function __construct(CustomizeMemberRepository $memberRepository,
                                CustomizeOrderRepository $orderRepository,
                                CustomizeOrderReviewRepository $reivewRepository,
                                CustomizeOrderClaimRepository $claimRepository,
                                CustomizeProductRepository $productRepository,
                                CustomizeProductInquireRepository $inquireRepository) {

        $this->memberRepository = $memberRepository;
        $this->orderRepository = $orderRepository;
        $this->claimRepository = $claimRepository;
        $this->inquireRepository = $inquireRepository;
        $this->reivewRepository = $reivewRepository;
        $this->productRepository = $productRepository;
    }


    public function getMainInfo() {

        ///////// 회원현황 /////////////
        $params['mstatus'] = 'ing';
        $params['dateKey'] = 'created_at';
        $params['dateValue'] = date('Y-m-d',mktime(1,1,1,date('m')-1,date('d'),date('Y')));

        $data['memberJoin'] = $this->memberRepository->getUserCount($params);

        $params['mstatus'] = 'out';
        $params['dateKey'] = 'updated_at';
        $data['memberOut'] = $this->memberRepository->getUserCount($params);

        ///////// 주문현황 /////////////
        $ordparams['dateKey'] = 'created_at';
        $ordparams['dateValue'] = date('Y-m-d',mktime(1,1,1,date('m')-1,date('d'),date('Y')));
        $data['order']['all'] = $this->orderRepository->getOrderAllCount($ordparams);
        $ordparams['ostatus'] = ['DR','DI','DC','OC'];
        $orderList = $this->orderRepository->getOrderCount($ordparams);
        $oidList = [];
        foreach($orderList as $val) {
            if(empty($oidList[$val->ostatus][$val->oid])) {
                $oidList[$val->ostatus][$val->oid] = 1;
                if(empty($data['order'][$val->ostatus]))$data['order'][$val->ostatus] = 1;
                else $data['order'][$val->ostatus] = $data['order'][$val->ostatus] + 1;
            }
        }

        ///////// 클레임현황 /////////////
        $claimparams['dateKey'] = 'created_at';
        $claimparams['dateValue'] = date('Y-m-d',mktime(1,1,1,date('m')-1,date('d'),date('Y')));
        $claimList = $this->claimRepository->getClaimCount($claimparams);
        foreach($claimList as $val) {
            if(!empty($data['claim'][$val->claimType])) {
                $data['claim'][$val->claimType] = $data['claim'][$val->claimType] + 1;
            } else {
                $data['claim'][$val->claimType] = 1;
            }
        }
         ///////// 상품문의 현황 /////////////
        $inquireParams['stdate'] = date('Y-m-d',mktime(1,1,1,date('m')-1,date('d'),date('Y')));
        $inquireParams['endate'] = date('Y-m-d');
        $data['inquireList'] = $this->inquireRepository->getProductInquireDataList($inquireParams,5);

        ///////// 상품후기 현황 /////////////
        $data['reviewList'] = $this->reivewRepository->getReviewDataList($inquireParams,5);

        $ptParams['orderByField'] = 'able_amt';
        $ptParams['orderBySort'] = 'asc';
        $ptParams['soldout'] = 'yes';

        $data['productInventoryList'] = $this->productRepository->getProductInventoryList($ptParams,5);
        $data['optionInventoryList'] = $this->productRepository->getOptionInventoryList($ptParams,5);

        return  $data;
    }



}
