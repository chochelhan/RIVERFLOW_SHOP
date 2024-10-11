<?php

namespace App\Services\Api\Core\Common;
use App\Repositories\Repository\Base\BaseSettingDeliveryRepository;
use App\Repositories\Repository\Base\BaseSettingDeliveryLocalRepository;

class CommonOrderDeliveryService {

     private   $localRepository;
     private   $deliveryRepository;
     private   $areaSameWords;

     public function __construct(BaseSettingDeliveryRepository $deliveryRepository,
                                 BaseSettingDeliveryLocalRepository $localRepository) {

            $this->localRepository = $localRepository;
            $this->deliveryRepository = $deliveryRepository;

            $this->areaSameWords = [
                '경상남도'=>'경남',
                '경상북도'=>'경북',
                '전라남도'=>'전남',
                '전라북도'=>'전북',
                '충청남도'=>'충남',
                '충청남도'=>'충북',
            ];
     }

     /**
     *
     *@ 배송비 가져오기
     *@ params ,totalPrice(최종할인상품금액)
     **/
     public function getDeliveryPrice(array $params,int $totalPrice) {
        $deliveryId = $params['deliveryId'];
        $addr1 = (!empty($params['jibunAddr1']))?$params['jibunAddr1']:'';
        $info = $this->deliveryRepository->getDeliveryInfo($deliveryId);
        if($info) {
            $deliveryPrice = 0;
            switch ($info->dpriceType) {
                case 'price':// 고정배송비
                    $deliveryPrice = $info->fprice;
                    break;
                case 'half':// 조건부
                    if($totalPrice >= $info->oprice) {
                        $deliveryPrice = 0;
                    } else {
                        $deliveryPrice = $info->mprice;
                    }
                    break;
            }
            $addPrice = 0;
            if(!empty($addr1)) {
                if($info->localUse == 'yes') { // 지역별 배송비 사용이 가능할때
                    $addrs = explode(' ',$addr1);

                    $localInfo = $this->localRepository->getDeliveryLocalInfo($info->localId);
                    if($localInfo && $localInfo->localData) {
                        $localDatas = json_decode($localInfo->localData);
                        foreach($localDatas as $data) {
                            if($data->name) {
                                $aresNames = explode(' ',$data->name);

                                if(!empty($this->areaSameWords[$aresNames[0]])) {
                                    $aresNames[0] = $this->areaSameWords[$aresNames[0]];
                                }
                                $areaFlag = false;
                                if(strpos($aresNames[0],$addrs[0])!==false) {
                                    $areaFlag = true;
                                    for($i=1; $i<count($aresNames); $i++) {
                                        if(empty($addrs[$i])) {
                                            $areaFlag = false;
                                        } else if($aresNames[$i]!= $addrs[$i]) {
                                            $areaFlag = false;

                                        }
                                    }
                                }
                                if($areaFlag) {
                                    $addPrice = (int)$data->price;
                                }
                            }
                        }
                    }
                }
            }
            $resultPrice = $deliveryPrice + $addPrice;
            return ['resultPrice'=>$resultPrice,
                    'basePrice'=>$deliveryPrice,
                    'addPrice'=>$addPrice];
        } else {
            return false;
        }

    }
}
