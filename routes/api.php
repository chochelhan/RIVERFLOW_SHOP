<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('apiGuard')->group(function () {
    /**
     * @ 기본설정 정보
     */
	Route::get('/controller/setting/getBase',[App\Http\Controllers\Api\Customize\CustomizeApiSettingViewController::class,'getBase']);

    /**
     * @ 메인설정 정보
     */
    Route::get('/controller/setting/getMain',[App\Http\Controllers\Api\Customize\CustomizeApiSettingViewController::class,'getMain']);


    /**
     * @ 로그인 아웃/ 회원가입
     */
    Route::get('/controller/member/getMemberConfig',[App\Http\Controllers\Api\Customize\CustomizeApiMemberViewController::class,'getMemberConfig']);
    Route::get('/controller/member/getMemberAgree',[App\Http\Controllers\Api\Customize\CustomizeApiMemberViewController::class,'getMemberAgree']);

    // 로그인
    Route::post('/controller/member/login',[App\Http\Controllers\Api\Customize\CustomizeApiLoginAuthController::class,'login']);


    // 로그 아웃
    Route::post('/controller/member/logout',[App\Http\Controllers\Api\Customize\CustomizeApiLoginAuthController::class,'logout']);

    // 회원가입
    Route::post('/controller/member/join',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'join']);
    Route::post('/controller/member/checkUid',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'checkUid']);
    Route::post('/controller/member/checkNick',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'checkNick']);
    Route::post('/controller/member/sendAuthEmail',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'sendAuthEmail']);
    Route::post('/controller/member/sendAuthPcs',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'sendAuthPcs']);
    Route::post('/controller/member/getAuthNumberConfirm',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'getAuthNumberConfirm']);

    // 비밀번호 찾기

    Route::post('/controller/member/findMemberUpass',[App\Http\Controllers\Api\Customize\CustomizeApiMemberController::class,'findMemberUpass']);



    /**
     * @ 마이페이지
     */

    Route::post('/controller/mypage/getMemberLevelName',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMemberLevelName']);
    Route::post('/controller/mypage/getMyMain',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyMain']);

    Route::post('/controller/mypage/getMyOrderList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getOrderList']);
    Route::post('/controller/mypage/getMyOrderDetail',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyOrderDetail']);
    Route::post('/controller/mypage/getMyPointList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyPointList']);
    Route::post('/controller/mypage/getMyOrderProductList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getOrderProductList']);

    Route::post('/controller/mypage/updateMemberImage',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'updateMemberImage']);
    Route::post('/controller/mypage/getMemberInfo',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMemberInfo']);

    //클레임 저장
    Route::post('/controller/mypage/insertMyOrderClaim',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'insertOrderClaim']);
    Route::post('/controller/mypage/getClaimCheckProductList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getClaimCheckProductList']);

    // 구매확정
    Route::post('/controller/mypage/updateOrderComplete',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'updateOrderComplete']);

    // 배송조회
    Route::post('/controller/mypage/getDeliveryTracker',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getDeliveryTracker']);

    /// 구매가능한 후기 목록
    Route::post('/controller/mypage/getMyAbleReviewOrderList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyAbleReviewOrderList']);
    /// 후기 목록
    Route::post('/controller/mypage/getMyReviewList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyReviewList']);
    /// 구매후기 저장
    Route::post('/controller/mypage/insertMyOrderReview',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'insertMyOrderReview']);
    /// 구매후기 정보
    Route::post('/controller/mypage/getMyReviewInfo',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyReviewInfo']);




    // 회원정보 수정시 닉네임 체크
    Route::post('/controller/mypage/checkMemberNick',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'checkMemberNick']);
    // 회원정보 수정
    Route::post('/controller/mypage/updateMemberInfo',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'updateMemberInfo']);
    // 배송지 관리
    Route::post('/controller/mypage/getMyShippingList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyShippingList']);
    Route::post('/controller/mypage/getMyShippingInfo',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyShippingInfo']);
    Route::post('/controller/mypage/updateMyShipping',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'updateMyShipping']);
    Route::post('/controller/mypage/deleteMyShipping',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'deleteMyShipping']);
    // 쿠폰
    Route::post('/controller/mypage/getMyCouponList',[App\Http\Controllers\Api\Customize\CustomizeApiMypageViewController::class,'getMyCouponList']);
    Route::post('/controller/mypage/updateMyCoupon',[App\Http\Controllers\Api\Customize\CustomizeApiMypageController::class,'updateMyCoupon']);

    // 관심상품
    Route::post('/controller/wish/getMyWishList',[App\Http\Controllers\Api\Customize\CustomizeApiWishController::class,'getMyWishList']);
    //관심상푸 추가 / 삭제
    Route::post('/controller/wish/updateProductWish',[App\Http\Controllers\Api\Customize\CustomizeApiWishController::class,'updateProductWish']);



    /**
     * @ 검색
     */
    Route::post('/controller/search/searchData',[App\Http\Controllers\Api\Customize\CustomizeApiSearchController::class,'searchData']);
    Route::post('/controller/search/getCategoryList',[App\Http\Controllers\Api\Customize\CustomizeApiSearchController::class,'getCategoryList']);



    /**
     * @ 상품
     */
    // 상품목록
    Route::get('/controller/product/getProductList',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductList']);
    Route::get('/controller/product/getProductDataList',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductDataList']);


    // 상품상세
    Route::get('/controller/product/getProductInfo',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductInfo']);
    // 상품과 연관된 다른상품
    Route::get('/controller/product/getProductRelationList',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductRelationList']);

    // 상품 리뷰
    Route::get('/controller/product/getProductReviewList',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductReviewList']);


    // 상품문의
    Route::post('/controller/product/insertProductInquire',[App\Http\Controllers\Api\Customize\CustomizeApiProductController::class,'insertProductInquire']);
    Route::get('/controller/product/getProductInquireList',[App\Http\Controllers\Api\Customize\CustomizeApiProductViewController::class,'getProductInquireList']);








    /**
     * @ 장바구니
     */
    // 임시장바구니 저장
    Route::post('/controller/cart/insertTempCart',[App\Http\Controllers\Api\Customize\CustomizeApiCartController::class,'insertTempCart']);
    // 장바구니 저장
    Route::post('/controller/cart/insertCart',[App\Http\Controllers\Api\Customize\CustomizeApiCartController::class,'insertCart']);
    // 장바구니 목록
    Route::get('/controller/cart/getCartList',[App\Http\Controllers\Api\Customize\CustomizeApiCartViewController::class,'getCartList']);
    // 장바구니 삭제
    Route::post('/controller/cart/deleteCart',[App\Http\Controllers\Api\Customize\CustomizeApiCartController::class,'deleteCart']);
    // 장바구니 구매수량 변경
    Route::post('/controller/cart/updateCartCamt',[App\Http\Controllers\Api\Customize\CustomizeApiCartController::class,'updateCartCamt']);




    /**
     * @ 주문
     */
    Route::post('/controller/order/orderRegistInfo',[App\Http\Controllers\Api\Customize\CustomizeApiOrderController::class,'orderRegistInfo']);
    Route::post('/controller/order/updateOrderPriceInfo',[App\Http\Controllers\Api\Customize\CustomizeApiOrderController::class,'updateOrderPriceInfo']);
    // 주문정보 저장
    Route::post('/controller/order/insertOrder',[App\Http\Controllers\Api\Customize\CustomizeApiOrderController::class,'insertOrder']);
    // 주문완료
    Route::post('/controller/order/getOrderComplete',[App\Http\Controllers\Api\Customize\CustomizeApiOrderController::class,'getOrderComplete']);


    /**
     * @ 게시판
     */
    // 게시판목록
    Route::get('/controller/board/getArticleList',[App\Http\Controllers\Api\Customize\CustomizeApiBoardViewController::class,'getArticleList']);
    Route::get('/controller/board/getArticleListByBtype',[App\Http\Controllers\Api\Customize\CustomizeApiBoardViewController::class,'getArticleListByBtype']);
    Route::get('/controller/board/getArticleInfo',[App\Http\Controllers\Api\Customize\CustomizeApiBoardViewController::class,'getArticleInfo']);
    Route::post('/controller/board/insertArticle',[App\Http\Controllers\Api\Customize\CustomizeApiBoardController::class,'insertArticle']);
    Route::post('/controller/board/updateArticle',[App\Http\Controllers\Api\Customize\CustomizeApiBoardController::class,'updateArticle']);
    Route::post('/controller/board/deleteArticle',[App\Http\Controllers\Api\Customize\CustomizeApiBoardController::class,'deleteArticle']);
    Route::post('/controller/board/checkArticleUserPass',[App\Http\Controllers\Api\Customize\CustomizeApiBoardController::class,'checkArticleUserPass']);
    Route::post('/controller/board/insertArticleTempImage',[App\Http\Controllers\Api\Customize\CustomizeApiBoardController::class,'insertArticleTempImage']);

    /**
     * @ 댓글
     */
    Route::get('/controller/comment/getCommentList',[App\Http\Controllers\Api\Customize\CustomizeApiCommentController::class,'getCommentList']);
    Route::post('/controller/comment/updateComment',[App\Http\Controllers\Api\Customize\CustomizeApiCommentController::class,'updateComment']);
    Route::post('/controller/comment/insertComment',[App\Http\Controllers\Api\Customize\CustomizeApiCommentController::class,'insertComment']);
    Route::post('/controller/comment/deleteComment',[App\Http\Controllers\Api\Customize\CustomizeApiCommentController::class,'deleteComment']);
});


/**
 * @ 배송추적 트랙커
 */
Route::post('/tracker/recordTracker',[App\Http\Controllers\Api\Core\TrackerController::class,'recordTracker']);
