<?php

namespace App\Repositories\Repository\Base;

use App\Repositories\Interface\MemberRepositoryInterface;
use App\Models\Customize\CustomizeMember;

class BaseMemberRepository implements MemberRepositoryInterface {

      protected $member;

      public function __construct(CustomizeMember $member) {
         $this->member = $member;

      }
      public function memberInfo(int $id) {

         return $this->member->find($id);
      }

      // uid 존재여부
      public function checkUid(string $uid) {
         $row = $this->member::where('uid',$uid)->first();
         return ($row)?true:false;
      }

      /// 적립금 변경
      public function updateMemberPoint(array $params) {

            $id = $params['user_id'];
            if(!$id)return;
            $row = $this->member::find($id);
            if(!$row->id)return;
            if(!$row->point)$row->point = 0;
            switch($params['ptype']) {
                case 'plus':
                   $point = $row->point + $params['point'];
                break;
                case 'minus':
                    $point = $row->point - $params['point'];
                    if($point<0)$point = 0;
                break;
            }
            $this->member::find($id)->update(['point'=>$point]);
     }
     // 회원정보 변경
     public function updateMember(int $id,array $updateData) {

        return $this->member::find($id)->update($updateData);
    }
}

