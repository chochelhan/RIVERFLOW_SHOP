import{a as A}from"./axios-c24e582b.js";const t="/admin/controller/",D=t+"auth/token",T=t+"member/login",c=t+"member/logout",r=t+"adminInfo/updateAdminInfo",P=t+"adminInfo/getAdminInfo",N=t+"setting/updateSiteEnv",i=t+"adminMain/getSiteTotalInfo",M=t+"setting/getSettingDeliveryList",d=t+"setting/insertSettingDelivery",R=t+"setting/updateSettingDelivery",g=t+"setting/deleteSettingDelivery",O=t+"setting/sequenceSettingDelivery",S=t+"setting/updateSettingDeliveryGroupType",L=t+"setting/insertSettingDeliveryLocal",u=t+"setting/updateSettingDeliveryLocal",G=t+"setting/getSettingDeliveryLocalInfo",C=t+"setting/deleteSettingDeliveryLocal",U=t+"setting/getDeliveryCompanyInfo",l=t+"setting/updateDeliveryCompany",p=t+"setting/getPaymentCompanyInfo",m=t+"setting/updatePaymentCompany",b=t+"setting/updateSettingCompany",B=t+"setting/getSettingCompany",f=t+"setting/updateSettingMember",h=t+"setting/getSettingMember",y=t+"setting/updateSettingAgree",Y=t+"setting/getSettingAgree",v=t+"setting/updateSettingImage",F=t+"setting/getSettingImage",V=t+"setting/updateSettingPoint",k=t+"setting/getSettingPoint",q=t+"setting/updateSettingMenu",Q=t+"setting/getSettingMenu",w=t+"setting/updateSettingLogo",H=t+"setting/getSettingLogo",x=t+"setting/getSettingMain",W=t+"setting/updateSettingMainBanner",K=t+"setting/updateSettingMainDisplay",J=t+"member/getViewAllList",X=t+"member/getMemberDataList",j=t+"member/getMemberInfoById",z=t+"member/checkIsMemberNick",Z=t+"member/updateMember",$=t+"member/updateMemberStatus",tt=t+"member/insertMemberLevel",_t=t+"member/updateMemberLevel",et=t+"member/deleteMemberLevel",st=t+"member/sequenceMemberLevel",It=t+"member/getMemberLevelList",at=t+"member/updateMemberPoint",At=t+"member/getPointList",ot=t+"product/insertProductCategory",Et=t+"product/updateProductCategory",nt=t+"product/deleteProductCategory",Dt=t+"product/sequenceProductCategory",Tt=t+"product/getProductCategoryList",ct=t+"product/insertProductBrand",rt=t+"product/updateProductBrand",Pt=t+"product/deleteProductBrand",Nt=t+"product/sequenceProductBrand",it=t+"product/getProductBrandList",Mt=t+"product/insertProductAddInfo",dt=t+"product/updateProductAddInfo",Rt=t+"product/getProductAddInfoList",gt=t+"product/deleteProductAddInfo",Ot=t+"product/sequenceProductAddInfo",St=t+"product/getProductProductList",Lt=t+"product/getProductProductDataList",ut=t+"product/getProductProductRegistInfo",Gt=t+"product/insertProductProduct",Ct=t+"product/updateProductProduct",Ut=t+"product/insertProductTempImage",lt=t+"product/getProductInfoNoticeList",pt=t+"product/deleteProductInfoNotice",mt=t+"product/getOrderReviewList",bt=t+"product/getOrderReviewDataList",Bt=t+"product/blindOrderReview",ft=t+"product/getOrderReviewInfo",ht=t+"product/getInquireList",yt=t+"product/getInquireDataList",Yt=t+"product/deleteInquire",vt=t+"product/getInquireInfo",Ft=t+"product/updateInquire",Vt=t+"inventory/getProductList",kt=t+"inventory/getProductDataList",qt=t+"inventory/getOptionList",Qt=t+"inventory/getOptionDataList",wt=t+"inventory/getHistoryList",Ht=t+"inventory/updateInventoryProduct",xt=t+"order/getOrderList",Wt=t+"order/getOrderDataList",Kt=t+"order/getOrderDetail",Jt=t+"order/updateOrderStatus",Xt=t+"order/updateClaimStatus",jt=t+"order/getCancleList",zt=t+"order/getCancleDataList",Zt=t+"order/getReturnList",$t=t+"order/getReturnDataList",t_=t+"order/getExchangeList",__=t+"order/getExchangeDataList",e_=t+"order/getRefundList",s_=t+"order/getRefundDataList",I_=t+"order/activeRefund",a_=t+"board/insertBoard",A_=t+"board/updateBoard",o_=t+"board/getBoardList",E_=t+"board/deleteBoard",n_=t+"board/sequenceBoard",D_=t+"board/getBoardArticleRegist",T_=t+"board/insertBoardArticle",c_=t+"board/updateBoardArticle",r_=t+"board/deleteBoardArticle",P_=t+"board/getBoardArticleList",N_=t+"board/getBoardArticleDataList",i_=t+"board/insertArticleTempImage",M_=t+"board/getBoardFaqRegist",d_=t+"board/getBoardFaqList",R_=t+"comment/updateComment",g_=t+"comment/insertComment",O_=t+"comment/getCommentList",S_=t+"statistics/getJoinMember",L_=t+"statistics/getOrder",u_=t+"statistics/getOrderMember",G_=t+"statistics/getOrderMemberDataList",C_=t+"statistics/getOrderProduct",U_=t+"statistics/getOrderProductDataList",l_=t+"sendSetting/getSmsEmailSetting",p_=t+"sendSetting/updateSmsEmailSetting",o={"Content-Type":"application/json;charset=UTF-8"},E={"Content-Type":"multipart/form-data;charset=utf-8;"};A.defaults.withCredentials=!0;const m_={DEBGU:!0,SHOPTYPE:"STANDARD",csrfToken:null,version:"1.0.0",post(_,I,a,s){this.checkDebug(),this.csrfToken&&(I._token=this.csrfToken),A.post(_,I,{headers:o}).then(e=>{switch(this.DEBGU&&console.log(e.data),e.data.status){case"success":case"message":a(e.data);break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(e=>{this.setError(e)})},postFile(_,I,a){this.checkDebug(),this.csrfToken&&I.append("_token",this.csrfToken),A.post(_,I,{headers:E}).then(s=>{switch(this.DEBGU&&console.log(s.data),s.data.status){case"success":case"message":a(s.data);break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(s=>{this.setError(s)})},get(_,I,a,s){this.checkDebug(),A.get(_,{params:I,headers:o}).then(e=>{if(this.DEBGU&&console.log(e.data),e.data.status=="notLogin"){location.href="/admin";return}switch(e.data.status){case"success":case"message":a(e.data);break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(e=>{s?s(e.response.data):this.setError(e)})},checkDebug(){location.hostname=="localhost"?this.DEBGU=!0:this.DEBGU=!1},setError(_){switch(_.response.status){case 419:alert("csrf토큰에 문제가 생겨 페이지를 새로고침 합니다"),location.href="/admin";return;case 401:alert("세션이 만료되서 로그아웃 되었습니다"),location.href="/admin";return;case 422:alert("잘못된 파일입니다");break}this.DEBGU&&(_.response?(console.log(_.response.data),console.log(_.response.status),console.log(_.response.headers)):_.request?console.log(_.request):console.log("Error",_.message))}};export{V as $,c as A,Nt as B,ct as C,rt as D,Rt as E,gt as F,Ot as G,Mt as H,dt as I,p as J,m as K,G as L,C as M,L as N,u as O,M as P,Ut as Q,g as R,O as S,S as T,d as U,R as V,U as W,l as X,h as Y,f as Z,k as _,m_ as a,T_ as a$,Y as a0,y as a1,F as a2,v as a3,H as a4,w as a5,Q as a6,q as a7,x as a8,W as a9,qt as aA,Qt as aB,Jt as aC,xt as aD,Wt as aE,Kt as aF,jt as aG,zt as aH,I_ as aI,Xt as aJ,Zt as aK,$t as aL,t_ as aM,__ as aN,e_ as aO,s_ as aP,P_ as aQ,N_ as aR,r_ as aS,o_ as aT,E_ as aU,n_ as aV,a_ as aW,A_ as aX,D_ as aY,i_ as aZ,c_ as a_,K as aa,l_ as ab,p_ as ac,St as ad,Lt as ae,lt as af,pt as ag,ut as ah,Gt as ai,Ct as aj,ft as ak,O_ as al,Bt as am,R_ as an,g_ as ao,mt as ap,bt as aq,vt as ar,Yt as as,Ft as at,ht as au,yt as av,Vt as aw,kt as ax,Ht as ay,wt as az,T as b,At as b0,at as b1,d_ as b2,M_ as b3,S_ as b4,u_ as b5,G_ as b6,C_ as b7,U_ as b8,L_ as b9,N as ba,D as c,i as d,P as e,r as f,j as g,Z as h,z as i,J as j,X as k,$ as l,It as m,et as n,st as o,tt as p,_t as q,B as r,b as s,Tt as t,nt as u,Dt as v,ot as w,Et as x,it as y,Pt as z};
