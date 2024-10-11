<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\Api\Customize\CustomizeApiMainController::class, 'getMain']);
Route::get('/main', [App\Http\Controllers\Api\Customize\CustomizeApiMainController::class, 'getMain']);
Route::get('/admin', [App\Http\Controllers\Api\Customize\CustomizeApiMainController::class, 'getAdminMain']);
Route::get('/admin/main', [App\Http\Controllers\Api\Customize\CustomizeApiMainController::class, 'getAdminMain']);


// 사용자 페이지 sns 로그인 콜백
Route::get('/snsController/member/snsCallBack/ka', [App\Http\Controllers\Api\Customize\CustomizeApiSnsCallbackController::class, 'kakaoLogin']);
Route::get('/snsController/member/snsCallBack/fb', [App\Http\Controllers\Api\Customize\CustomizeApiSnsCallbackController::class, 'facebookLogin']);
Route::get('/snsController/member/snsCallBack/nv', [App\Http\Controllers\Api\Customize\CustomizeApiSnsCallbackController::class, 'naverLogin']);


Route::get('/admin/controller/auth/token', [App\Http\Controllers\Admin\Customize\CustomizeLoginAuthController::class, 'checkLogin']);

Route::post('/admin/controller/member/login', [App\Http\Controllers\Admin\Customize\CustomizeLoginAuthController::class, 'login'])
    ->middleware('guest');

Route::post('/admin/controller/member/logout', [App\Http\Controllers\Admin\Customize\CustomizeLoginAuthController::class, 'logout'])
    ->middleware('auth');

/**
 * @ 관리자 정보
 */
Route::post('/admin/controller/adminInfo/getAdminInfo', [App\Http\Controllers\Admin\Customize\CustomizeAdminInfoController::class, 'getAdminInfo'])
    ->middleware('auth');

Route::post('/admin/controller/adminInfo/updateAdminInfo', [App\Http\Controllers\Admin\Customize\CustomizeAdminInfoController::class, 'updateAdminInfo'])
    ->middleware('auth');


/**
 * @ 사이트 토탈 정보
 */
Route::post('/admin/controller/adminMain/getSiteTotalInfo', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getSiteTotalInfo'])
    ->middleware('auth');


/**
 * @ 실행환경 설정
 */
Route::post('/admin/controller/setting/updateSiteEnv', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateSiteEnv'])
    ->middleware('auth');


/**
 * @ 기본설정
 */
/*** 배송비 ***/
Route::post('/admin/controller/setting/getSettingDeliveryList', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getDeliveryList'])
    ->middleware('auth');
Route::post('/admin/controller/setting/insertSettingDelivery', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'insertDelivery'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingDelivery', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateDelivery'])
    ->middleware('auth');
Route::post('/admin/controller/setting/deleteSettingDelivery', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'deleteDelivery'])
    ->middleware('auth');
Route::post('/admin/controller/setting/sequenceSettingDelivery', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'sequenceDelivery'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingDeliveryGroupType', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateDeliveryGroupType'])
    ->middleware('auth');


/***  배송비 추가 지역별 배송비 ***/
Route::post('/admin/controller/setting/getSettingDeliveryLocalInfo', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getDeliveryLocalInfo'])
    ->middleware('auth');
Route::post('/admin/controller/setting/insertSettingDeliveryLocal', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'insertDeliveryLocal'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingDeliveryLocal', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateDeliveryLocal'])
    ->middleware('auth');
Route::post('/admin/controller/setting/deleteSettingDeliveryLocal', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'deleteDeliveryLocal'])
    ->middleware('auth');

/***  배송업체정보 설정 ***/
Route::post('/admin/controller/setting/getDeliveryCompanyInfo', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getDeliveryCompanyInfo'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateDeliveryCompany', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateDeliveryCompany'])
    ->middleware('auth');


/***  PG정보 설정 ***/
Route::post('/admin/controller/setting/getPaymentCompanyInfo', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getPaymentCompanyInfo'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updatePaymentCompany', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updatePaymentCompany'])
    ->middleware('auth');


/***  업체정보 설정 ***/
Route::post('/admin/controller/setting/updateSettingCompany', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateCompany'])
    ->middleware('auth');
Route::post('/admin/controller/setting/getSettingCompany', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getCompany'])
    ->middleware('auth');


/***  회원가입정보 설정 ***/
Route::post('/admin/controller/setting/updateSettingMember', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateMember'])
    ->middleware('auth');
Route::post('/admin/controller/setting/getSettingMember', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getMember'])
    ->middleware('auth');

/***  약관정보 설정 ***/
Route::post('/admin/controller/setting/updateSettingAgree', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateAgree'])
    ->middleware('auth');
Route::post('/admin/controller/setting/getSettingAgree', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getAgree'])
    ->middleware('auth');

/***  이미지 크기 정보 설정 ***/
Route::post('/admin/controller/setting/updateSettingImage', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateImage'])
    ->middleware('auth');
Route::post('/admin/controller/setting/getSettingImage', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getImage'])
    ->middleware('auth');

/***  적립금 정보 설정 ***/
Route::post('/admin/controller/setting/updateSettingPoint', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updatePoint'])
    ->middleware('auth');
Route::post('/admin/controller/setting/getSettingPoint', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getPoint'])
    ->middleware('auth');

/**
 * @ 사이트 설정
 */

/***  로고 설정 ***/
Route::post('/admin/controller/setting/getSettingLogo', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getLogo'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingLogo', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateLogo'])
    ->middleware('auth');


/***  메뉴 설정 ***/
Route::post('/admin/controller/setting/getSettingMenu', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getMenu'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingMenu', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateMenu'])
    ->middleware('auth');

/***  메인페이지 설정 ***/
Route::post('/admin/controller/setting/getSettingMain', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getMain'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingMainBanner', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateMainBanner'])
    ->middleware('auth');
Route::post('/admin/controller/setting/updateSettingMainDisplay', [App\Http\Controllers\Admin\Customize\CustomizeSettingController::class, 'updateMainDisplay'])
    ->middleware('auth');

/***  서버정보 ***/
Route::post('/admin/controller/setting/getServerPath', [App\Http\Controllers\Admin\Customize\CustomizeSettingViewController::class, 'getServerPath'])
    ->middleware('auth');


/**
 * @ 회원
 */
/*** 회원 정보 ***/
Route::post('/admin/controller/member/getViewAllList', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getAllList'])
    ->middleware('auth');
Route::post('/admin/controller/member/getMemberDataList', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getMemberDataList'])
    ->middleware('auth');

Route::post('/admin/controller/member/getMemberInfoById', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getMemberInfoById'])
	->middleware('auth');
Route::post('/admin/controller/member/checkIsMemberNick', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'checkIsMemberNick'])
	->middleware('auth');
Route::post('/admin/controller/member/updateMember', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'updateMember'])
	->middleware('auth');
Route::post('/admin/controller/member/updateMemberStatus', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'updateMemberStatus'])
	->middleware('auth');


/*** 회원 등급 ***/
Route::post('/admin/controller/member/insertMemberLevel', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'insertLevel'])
    ->middleware('auth');
Route::post('/admin/controller/member/updateMemberLevel', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'updateLevel'])
    ->middleware('auth');
Route::post('/admin/controller/member/deleteMemberLevel', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'deleteLevel'])
    ->middleware('auth');
Route::post('/admin/controller/member/sequenceMemberLevel', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'sequenceLevel'])
    ->middleware('auth');
Route::post('/admin/controller/member/getMemberLevelList', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getLevelList'])
    ->middleware('auth');

/*** 회원 적리급 지급/차감 ***/
Route::post('/admin/controller/member/updateMemberPoint', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'updateMemberPoint'])
    ->middleware('auth');

/*** 회원 적리급 내역 ***/
Route::post('/admin/controller/member/getPointList', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getPointList'])
    ->middleware('auth');

/*** 회원 목록 (쿠폰목록 포함) ***/
Route::post('/admin/controller/member/getMemberListByCoupon', [App\Http\Controllers\Admin\Customize\CustomizeMemberViewController::class, 'getMemberListByCoupon'])
    ->middleware('auth');
/*** 회원 쿠폰 지급 ***/
Route::post('/admin/controller/member/updateMemberCoupon', [App\Http\Controllers\Admin\Customize\CustomizeMemberController::class, 'updateMemberCoupon'])
    ->middleware('auth');


/**
 * @ 상품
 */
/*** 카테고리 ***/
Route::post('/admin/controller/product/insertProductCategory', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'insertCategory'])
    ->middleware('auth');
Route::post('/admin/controller/product/updateProductCategory', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'updateCategory'])
    ->middleware('auth');
Route::post('/admin/controller/product/deleteProductCategory', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'deleteCategory'])
    ->middleware('auth');
Route::post('/admin/controller/product/sequenceProductCategory', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'sequenceCategory'])
    ->middleware('auth');
Route::post('/admin/controller/product/getProductCategoryList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getCategoryList'])
    ->middleware('auth');

/*** 브랜드 ***/
Route::post('/admin/controller/product/insertProductBrand', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'insertBrand'])
    ->middleware('auth');
Route::post('/admin/controller/product/updateProductBrand', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'updateBrand'])
    ->middleware('auth');
Route::post('/admin/controller/product/deleteProductBrand', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'deleteBrand'])
    ->middleware('auth');
Route::post('/admin/controller/product/sequenceProductBrand', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'sequenceBrand'])
    ->middleware('auth');
Route::post('/admin/controller/product/getProductBrandList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getBrandList'])
    ->middleware('auth');


/*** 상품 추가 정보 ***/
Route::post('/admin/controller/product/insertProductAddInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'insertAddInfo'])
    ->middleware('auth');
Route::post('/admin/controller/product/updateProductAddInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'updateAddInfo'])
    ->middleware('auth');
Route::post('/admin/controller/product/deleteProductAddInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'deleteAddInfo'])
    ->middleware('auth');
Route::post('/admin/controller/product/sequenceProductAddInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'sequenceAddInfo'])
    ->middleware('auth');
Route::post('/admin/controller/product/getProductAddInfoList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getAddInfoList'])
    ->middleware('auth');

/*** 상품 ***/
Route::post('/admin/controller/product/getProductProductList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getProductList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getProductProductDataList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getProductDataList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getProductProductRegistInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getProductRegistInfo'])
    ->middleware('auth');
Route::post('/admin/controller/product/insertProductProduct', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'insertProduct'])
    ->middleware('auth');
Route::post('/admin/controller/product/updateProductProduct', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'updateProduct'])
    ->middleware('auth');
Route::post('/admin/controller/product/insertProductTempImage', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'insertProductTempImage'])
    ->middleware('auth');

/// 상품제공고시 불러오기
Route::post('/admin/controller/product/getProductInfoNoticeList', [App\Http\Controllers\Admin\Customize\CustomizeProductViewController::class, 'getProductInfoNoticeList'])
    ->middleware('auth');
Route::post('/admin/controller/product/deleteProductInfoNotice', [App\Http\Controllers\Admin\Customize\CustomizeProductController::class, 'deleteProductInfoNotice'])
    ->middleware('auth');


/**
 * @ 상품(구매) 후기
 */
Route::post('/admin/controller/product/getOrderReviewList', [App\Http\Controllers\Admin\Customize\CustomizeOrderReviewViewController::class, 'getReviewList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getOrderReviewDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderReviewViewController::class, 'getReviewDataList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getOrderReviewInfo', [App\Http\Controllers\Admin\Customize\CustomizeOrderReviewViewController::class, 'getReviewInfo'])
    ->middleware('auth');
// 블라인드 처리
Route::post('/admin/controller/product/blindOrderReview', [App\Http\Controllers\Admin\Customize\CustomizeOrderReviewController::class, 'blindReview'])
    ->middleware('auth');


/**
 * @ 상품 문의
 */
Route::post('/admin/controller/product/getInquireList', [App\Http\Controllers\Admin\Customize\CustomizeProductInquireViewController::class, 'getInquireList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getInquireDataList', [App\Http\Controllers\Admin\Customize\CustomizeProductInquireViewController::class, 'getInquireDataList'])
    ->middleware('auth');
Route::post('/admin/controller/product/getInquireInfo', [App\Http\Controllers\Admin\Customize\CustomizeProductInquireViewController::class, 'getInquireInfo'])
    ->middleware('auth');
// 삭제
Route::post('/admin/controller/product/deleteInquire', [App\Http\Controllers\Admin\Customize\CustomizeProductInquireController::class, 'deleteInquire'])
    ->middleware('auth');
// 수정
Route::post('/admin/controller/product/updateInquire', [App\Http\Controllers\Admin\Customize\CustomizeProductInquireController::class, 'updateInquire'])
    ->middleware('auth');


/**
 * @ 상품재고
 */
Route::post('/admin/controller/inventory/getProductList', [App\Http\Controllers\Admin\Customize\CustomizeInventoryViewController::class, 'getProductList'])
    ->middleware('auth');
Route::post('/admin/controller/inventory/getProductDataList', [App\Http\Controllers\Admin\Customize\CustomizeInventoryViewController::class, 'getProductDataList'])
    ->middleware('auth');
Route::post('/admin/controller/inventory/getOptionList', [App\Http\Controllers\Admin\Customize\CustomizeInventoryViewController::class, 'getOptionList'])
    ->middleware('auth');
Route::post('/admin/controller/inventory/getOptionDataList', [App\Http\Controllers\Admin\Customize\CustomizeInventoryViewController::class, 'getOptionDataList'])
    ->middleware('auth');
Route::post('/admin/controller/inventory/getHistoryList', [App\Http\Controllers\Admin\Customize\CustomizeInventoryViewController::class, 'getHistoryList'])
    ->middleware('auth');
Route::post('/admin/controller/inventory/updateInventoryProduct', [App\Http\Controllers\Admin\Customize\CustomizeInventoryController::class, 'updateInventoryProduct'])
    ->middleware('auth');


/**
 * @ 주문
 */
/*** 주문목록 ***/
Route::post('/admin/controller/order/getOrderList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getOrderList'])
    ->middleware('auth');
Route::post('/admin/controller/order/getOrderDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getOrderDataList'])
    ->middleware('auth');
/// 주문 상세 ///
Route::post('/admin/controller/order/getOrderDetail', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getOrderDetail'])
    ->middleware('auth');
/// 주문 상태 변경 ///
Route::post('/admin/controller/order/updateOrderStatus', [App\Http\Controllers\Admin\Customize\CustomizeOrderController::class, 'updateOrderStatus'])
    ->middleware('auth');

/// 클레임 상태 변경 ///
Route::post('/admin/controller/order/updateClaimStatus', [App\Http\Controllers\Admin\Customize\CustomizeOrderController::class, 'updateClaimStatus'])
    ->middleware('auth');


/**** 주문 취소 ****/
Route::post('/admin/controller/order/getCancleList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getCancleList'])
    ->middleware('auth');
Route::post('/admin/controller/order/getCancleDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getCancleDataList'])
    ->middleware('auth');

/**** 주문 반품 ****/
Route::post('/admin/controller/order/getReturnList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getReturnList'])
    ->middleware('auth');
Route::post('/admin/controller/order/getReturnDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getReturnDataList'])
    ->middleware('auth');

/**** 주문 교환 ****/
Route::post('/admin/controller/order/getExchangeList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getExchangeList'])
    ->middleware('auth');
Route::post('/admin/controller/order/getExchangeDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getExchangeDataList'])
    ->middleware('auth');

/**** 주문 환불 ****/
Route::post('/admin/controller/order/getRefundList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getRefundList'])
    ->middleware('auth');
Route::post('/admin/controller/order/getRefundDataList', [App\Http\Controllers\Admin\Customize\CustomizeOrderViewController::class, 'getRefundDataList'])
    ->middleware('auth');
Route::post('/admin/controller/order/activeRefund', [App\Http\Controllers\Admin\Customize\CustomizeOrderController::class, 'activeRefund'])
    ->middleware('auth');


/**
 * @ 게시판
 */
Route::post('/admin/controller/board/insertBoard', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'insertBoard'])
    ->middleware('auth');
Route::post('/admin/controller/board/updateBoard', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'updateBoard'])
    ->middleware('auth');
Route::post('/admin/controller/board/getBoardList', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getBoardList'])
    ->middleware('auth');
/*
Route::post('/admin/controller/board/getBoard',[App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class,'getBoard'])
        ->middleware('auth');
        */
Route::post('/admin/controller/board/deleteBoard', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'deleteBoard'])
    ->middleware('auth');
Route::post('/admin/controller/board/sequenceBoard', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'sequenceBoard'])
    ->middleware('auth');


//// 게시글 정보
Route::post('/admin/controller/board/getBoardArticleRegist', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getArticleRegist'])
    ->middleware('auth');
Route::post('/admin/controller/board/insertBoardArticle', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'insertArticle'])
    ->middleware('auth');
Route::post('/admin/controller/board/updateBoardArticle', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'updateArticle'])
    ->middleware('auth');
Route::post('/admin/controller/board/deleteBoardArticle', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'deleteArticle'])
    ->middleware('auth');
Route::post('/admin/controller/board/getBoardArticleList', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getArticleList'])
    ->middleware('auth');
Route::post('/admin/controller/board/getBoardArticleDataList', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getArticleDataList'])
    ->middleware('auth');
Route::post('/admin/controller/board/insertArticleTempImage', [App\Http\Controllers\Admin\Customize\CustomizeBoardController::class, 'insertArticleTempImage'])
    ->middleware('auth');


// faq 게시판
Route::post('/admin/controller/board/getBoardFaqList', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getFaqList'])
    ->middleware('auth');
Route::post('/admin/controller/board/getBoardFaqRegist', [App\Http\Controllers\Admin\Customize\CustomizeBoardViewController::class, 'getFaqRegist'])
    ->middleware('auth');


/**
 * @ 쿠폰
 */
Route::post('/admin/controller/coupon/insertCoupon', [App\Http\Controllers\Admin\Customize\CustomizeCouponController::class, 'insertCoupon'])
    ->middleware('auth');
Route::post('/admin/controller/coupon/updateCoupon', [App\Http\Controllers\Admin\Customize\CustomizeCouponController::class, 'updateCoupon'])
    ->middleware('auth');
Route::post('/admin/controller/coupon/getCouponRegistInfo', [App\Http\Controllers\Admin\Customize\CustomizeCouponViewController::class, 'getCouponRegistInfo'])
    ->middleware('auth');
Route::post('/admin/controller/coupon/getCouponList', [App\Http\Controllers\Admin\Customize\CustomizeCouponViewController::class, 'getCouponList'])
    ->middleware('auth');
/// 쿠폰 발행 내역
Route::post('/admin/controller/coupon/getCouponPublishList', [App\Http\Controllers\Admin\Customize\CustomizeCouponViewController::class, 'getCouponPublishList'])
    ->middleware('auth');
Route::post('/admin/controller/coupon/getCouponPublishDataList', [App\Http\Controllers\Admin\Customize\CustomizeCouponViewController::class, 'getCouponPublishDataList'])
    ->middleware('auth');

/**
 * @ 댓글
 */
Route::post('/admin/controller/comment/insertComment', [App\Http\Controllers\Admin\Customize\CustomizeCommentController::class, 'insertComment'])
    ->middleware('auth');
Route::post('/admin/controller/comment/updateComment', [App\Http\Controllers\Admin\Customize\CustomizeCommentController::class, 'updateComment'])
    ->middleware('auth');
Route::post('/admin/controller/comment/getCommentList', [App\Http\Controllers\Admin\Customize\CustomizeCommentViewController::class, 'getCommentList'])
    ->middleware('auth');


/**
 * @ 통계
 */
Route::post('/admin/controller/statistics/getJoinMember', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getJoinMember'])
    ->middleware('auth');
Route::post('/admin/controller/statistics/getOrder', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getOrder'])
    ->middleware('auth');
Route::post('/admin/controller/statistics/getOrderMember', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getOrderMember'])
    ->middleware('auth');
Route::post('/admin/controller/statistics/getOrderMemberDataList', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getOrderMemberDataList'])
    ->middleware('auth');
Route::post('/admin/controller/statistics/getOrderProduct', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getOrderProduct'])
    ->middleware('auth');
Route::post('/admin/controller/statistics/getOrderProductDataList', [App\Http\Controllers\Admin\Customize\CustomizeStatisticsViewController::class, 'getOrderProductDataList'])
    ->middleware('auth');

/**
 * @ 문자/이메일설정
 */
Route::post('/admin/controller/sendSetting/updateSmsEmailSetting', [App\Http\Controllers\Admin\Customize\CustomizeSmsEmailController::class, 'updateSmsEmailSetting'])
    ->middleware('auth');
Route::post('/admin/controller/sendSetting/getSmsEmailSetting', [App\Http\Controllers\Admin\Customize\CustomizeSmsEmailViewController::class, 'getSmsEmailSetting'])
    ->middleware('auth');



