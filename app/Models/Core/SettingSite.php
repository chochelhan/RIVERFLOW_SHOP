<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class SettingSite extends Model {

    public $table;

    protected $fillable = [
        'delivery',
        'member',
        'company',
        'agrees',
        'images',
        'points',
        'coupons',
        'order',
        'logo',
        'menu',
        'mainPage',
        'siteEnv',
    ];
    /**
    *
    *@ 각 설정별 설명

    point: {
        denyPoint: 적립금 지급제외 ['pp' (적립금결제시), 'cp'(쿠폰할인사용시)]
        expireNoticDay:
        expireNotice: 적립금 만료일 알림 시점 (yes,no)
        expireNoticeType: 알림수단 'sms', 'email'
        joinPoint: 회원가입 적립금 (int)
        joinPointUse: 회원가입 적립금 사용여부 yes,no
        oreplyPhotoPoint: 포토후기 적립금 (int)
        oreplyPoint: 일반후기 적립금 (int)
        oreplyPointUse: 구매후기 적립금 사용여부 yes,no
        payExpireMonth: 0 (적립금 유효기가 월)
        payExpireYear: 1 (적립금 유효기가 년)
        payTime:구매확정후 지급시점 (once(즉시), 2,7, 15 ... 일자)
        pointUnit: 적립금 표시명칭
        usePointMaxUse: 적립금 최대 사용가능금액 사용유무 (yes,no)
        usePointMax: % 로 나타남
        usePointMinPriceUse: 적립금 사용 최소 결제금액 사용유무 (yes,no)
        usePointMinPrice: 최소 결제금액 (int)
        usePointMinUse: 적립금 최소 사용가능금액 사용유무 (yes,no)
        usePointMin: 최소 사용가능 적립금 (int)

    }

    *
    */

    public function __construct() {
        $this->table = config('tables.settingSite');
    }

}
