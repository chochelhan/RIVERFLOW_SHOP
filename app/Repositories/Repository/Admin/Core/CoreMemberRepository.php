<?php

namespace App\Repositories\Repository\Admin\Core;

use App\Repositories\Repository\Base\BaseMemberRepository;


class CoreMemberRepository extends BaseMemberRepository
{

	public function getMemberById($id) {
		return $this->member::find($id);
	}
	// 회원 리스트
	public function getMemberList(array $params)
	{

		$orderByField = (!empty($params['orderByField'])) ? $params['orderByField'] : 'id';
		$orderBySort = (!empty($params['orderBySort'])) ? $params['orderBySort'] : 'desc';
		$this->member->setQueryParams($params);

		$list = $this->member::orderBy($orderByField, $orderBySort)
			->where(function ($query) {
				$queryParams = $this->member->queryParams;
				if (!empty($queryParams['keyword'])) {
					$query->where($queryParams['keywordCmd'], 'like', '%' . $queryParams['keyword'] . '%');
				}
				if (!empty($queryParams['mstatus'])) {
					$query->where('mstatus', $queryParams['mstatus']);
				}
				if (!empty($queryParams['stdate']) || !empty($queryParams['endate'])) {
					if (!empty($queryParams['stdate'])) {
						$query->where($queryParams['dateCmd'], '>=', $queryParams['stdate'] . ' 00:00:00');
					}
					if (!empty($queryParams['endate'])) {
						$query->where($queryParams['dateCmd'], '<=', $queryParams['endate'] . ' 23:59:59');
					}
				}

			})
			->paginate($params['limit']);
		return $list;
	}


	public function getUserCount(array $params)
	{
		return $this->member::where('mstatus', $params['mstatus'])->whereDate($params['dateKey'], '>=', $params['dateValue'])->count();
	}
	public function checkIsMemberNick(int $id,string $nick) {
		return $this->member::where('nick',$nick)->where('id','!=',$id)->first();
	}

}

