import{a as i}from"./axios-c24e582b.js";const e="/api/controller/",A=e+"setting/getBase",T=e+"setting/getMain",g=e+"member/getMemberConfig",L=e+"member/getMemberAgree",P=e+"member/login",m=e+"member/logout",l=e+"member/join",U=e+"member/checkUid",d=e+"member/checkNick",S=e+"member/sendAuthEmail",M=e+"member/sendAuthPcs",u=e+"member/getAuthNumberConfirm",f=e+"member/findMemberUpass",h=e+"mypage/getMyMain",G=e+"mypage/getMyOrderList",k=e+"mypage/getMyOrderDetail",C=e+"mypage/getMyPointList",p=e+"mypage/insertMyOrderClaim",O=e+"mypage/getClaimCheckProductList",D=e+"mypage/updateOrderComplete",N=e+"mypage/getDeliveryTracker",b=e+"mypage/insertMyOrderReview",y=e+"mypage/getMyAbleReviewOrderList",B=e+"mypage/getMyReviewList",w=e+"mypage/getMyReviewInfo",Y=e+"mypage/getMemberLevelName",H=e+"mypage/getMemberInfo",F=e+"mypage/updateMemberImage",J=e+"mypage/checkMemberNick",v=e+"mypage/updateMemberInfo",K=e+"mypage/getMyShippingList",W=e+"mypage/getMyShippingInfo",z=e+"mypage/updateMyShipping",V=e+"mypage/deleteMyShipping",q=e+"product/getProductList",j=e+"product/getProductDataList",x=e+"product/getProductInfo",Q=e+"product/getProductRelationList",X=e+"product/getProductReviewList",Z=e+"wish/updateProductWish",$=e+"product/insertProductInquire",ee=e+"product/getProductInquireList",te=e+"cart/insertTempCart",se=e+"cart/insertCart",oe=e+"cart/getCartList",ae=e+"cart/deleteCart",ne=e+"cart/updateCartCamt",_e=e+"wish/getMyWishList",re=e+"search/searchData",ce=e+"search/getCategoryList",Ie=e+"order/orderRegistInfo",ie=e+"order/updateOrderPriceInfo",Ee=e+"order/insertOrder",Re=e+"order/getOrderComplete",Ae=e+"board/getArticleListByBtype",Te=e+"board/getArticleList",ge=e+"board/getArticleInfo",Le=e+"board/insertArticle",Pe=e+"board/updateArticle",me=e+"board/deleteArticle",le=e+"board/checkArticleUserPass",Ue=e+"board/insertArticleTempImage",de=e+"comment/getCommentList",Se=e+"comment/updateComment",Me=e+"comment/insertComment",ue=e+"comment/deleteComment",c={Authorization:"","X-CSRF-TOKEN":window.csrfToken,"Content-Type":"application/json;charset=UTF-8"},E={Authorization:"","X-CSRF-TOKEN":window.csrfToken,"Content-Type":"multipart/form-data;charset=utf-8;"},r={device:!1,autoLogin:!0,setDevice(t){this.device=t,this.device?this.autoLogin=!0:this.autoLogin=localStorage.getItem("autoLogin")=="yes"},getSession(){let t={};if(this.autoLogin){if(localStorage.getItem("memberInfo")&&localStorage.getItem("tokenInfo")){const s=JSON.parse(localStorage.getItem("memberInfo")),a=JSON.parse(localStorage.getItem("tokenInfo"));s!=null&&s.id&&(a!=null&&a.token)&&(t.memberInfo=s,t.tokenInfo=a)}}else if(sessionStorage.getItem("memberInfo")&&sessionStorage.getItem("tokenInfo")){const s=JSON.parse(sessionStorage.getItem("memberInfo")),a=JSON.parse(sessionStorage.getItem("tokenInfo"));s!=null&&s.id&&(a!=null&&a.token)&&(t.memberInfo=s,t.tokenInfo=a)}return t},setSession(t){localStorage.setItem("old",JSON.stringify(t.tokenInfo)),this.autoLogin?(localStorage.setItem("memberInfo",JSON.stringify(t.userInfo)),localStorage.setItem("tokenInfo",JSON.stringify(t.tokenInfo))):(sessionStorage.setItem("memberInfo",JSON.stringify(t.userInfo)),sessionStorage.setItem("tokenInfo",JSON.stringify(t.tokenInfo))),c.Authorization="Bearer "+t.tokenInfo.token,E.Authorization="Bearer "+t.tokenInfo.token},updateSession(t){this.autoLogin?localStorage.setItem("memberInfo",JSON.stringify(t)):sessionStorage.setItem("memberInfo",JSON.stringify(t))},updateToken(t){console.log(t);const s={token:t.token};localStorage.setItem("new",JSON.stringify(s)),this.autoLogin?localStorage.setItem("tokenInfo",JSON.stringify(s)):sessionStorage.setItem("tokenInfo",JSON.stringify(s))},logout(){this.autoLogin?(localStorage.setItem("memberInfo",""),localStorage.setItem("tokenInfo","")):(sessionStorage.setItem("memberInfo",""),sessionStorage.setItem("tokenInfo","")),localStorage.setItem("autoLogin","no"),c.Authorization="",E.Authorization=""}},fe={DEBGU:!0,post(t,s,a,n){this.checkDebug(),this.checkBearerToken(),i.post(t,s,{headers:c}).then(o=>{var _,I;switch((_=o.data)!=null&&_.newToken&&((I=o.data)==null?void 0:I.newToken.status)=="success"&&r.updateToken(o.data.newToken),this.DEBGU&&console.log(o.data),o.data.status){case"success":case"message":a(o.data);break;case"notLogin":r.logout(),location.href="/";break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(o=>{this.setError(o)})},async asyncPost(t,s){return this.checkBearerToken(),await i.post(t,s,{headers:c})},postFile(t,s,a){this.checkDebug(),this.checkBearerToken(),i.post(t,s,{headers:E}).then(n=>{var o,_;switch((o=n.data)!=null&&o.newToken&&((_=n.data)==null?void 0:_.newToken.status)=="success"&&r.updateToken(n.data.newToken),this.DEBGU&&console.log(n.data),n.data.status){case"success":case"message":a(n.data);break;case"notLogin":r.logout(),location.href="/";break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(n=>{this.setError(n)})},get(t,s,a,n){this.checkDebug(),this.checkBearerToken(),i.get(t,{params:s,headers:c}).then(o=>{var _,I;switch((_=o.data)!=null&&_.newToken&&((I=o.data)==null?void 0:I.newToken.status)=="success"&&r.updateToken(o.data.newToken),this.DEBGU&&console.log(o.data),o.data.status){case"success":case"message":a(o.data);break;case"notLogin":r.logout(),location.href="/";break;case"error":alert("결과값이 없습니다");break;case"fail":alert("잘못된 접근입니다");break}}).catch(o=>{this.setError(o),n&&n(o.response.data)})},checkDebug(){location.hostname=="localhost"?this.DEBGU=!0:this.DEBGU=!1},checkBearerToken(){const t=r.getSession();if(t!=null&&t.memberInfo){const s=t.tokenInfo.token;c.Authorization="Bearer "+s,E.Authorization="Bearer "+s}},setError(t){if(!this.DEBGU){t.response.data=="tokenError"&&(alert("보안토큰이 만료되었습니다 페이지를 다시 로드합니다"),location.reload());return}t.response?(console.log(t.response.data),console.log(t.response.status),console.log(t.response.headers)):t.request?console.log(t.request):console.log("Error",t.message)}};export{_e as $,r as A,p as B,y as C,B as D,w as E,b as F,C as G,H,v as I,J,K,V as L,z as M,W as N,q as O,j as P,Z as Q,ee as R,$ as S,X as T,Q as U,x as V,se as W,te as X,oe as Y,ne as Z,ae as _,fe as a,ce as a0,re as a1,Te as a2,ge as a3,Ue as a4,Pe as a5,Le as a6,de as a7,ue as a8,Se as a9,Me as aa,me as ab,le as ac,Ae as ad,A as b,m as c,T as d,g as e,P as f,f as g,U as h,d as i,S as j,M as k,u as l,L as m,l as n,Ie as o,ie as p,Ee as q,Re as r,F as s,Y as t,h as u,D as v,k as w,N as x,G as y,O as z};
