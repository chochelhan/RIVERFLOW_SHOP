import{a as n,ba as l}from"./apiService-0aea4262.js";import{a}from"./admin-36cebdfb.js";import{_ as v,i as c,m as p,r,o as m,c as _,a as s,d as u,a0 as f,p as h,f as E}from"./index-e9ed42ea.js";import"./axios-c24e582b.js";const V={computed:{...c(a,["siteEnv"])},watch:{siteEnvVal(t){if(!this.wait){this.wait=!0;const e={type:"confirm",message:"실행환경을 변경하시겠습니까?",doAction:()=>{this.updateEnvMode()},cancleAction:()=>{this.siteEnvVal=this.oldSiteEnv,setTimeout(()=>{this.wait=!1},100)}};this.emitter.emit("modalOpen",e)}}},data(){return{siteEnvVal:"developer",sitenEnvList:[{value:"developer",title:"개발자 모드"},{value:"production",title:"실서비스 모드"}],oldSiteEnv:"developer",wait:!0}},created(){this.getSiteEnv?(this.siteEnvVal=this.siteEnv,setTimeout(()=>{this.wait=!1},200)):setTimeout(()=>{this.siteEnvVal=this.siteEnv,setTimeout(()=>{this.wait=!1},200)},200)},methods:{...p(a,["setSiteEnv"]),updateEnvMode(){this.wait&&(this.emitter.emit("overlay","open"),n.post(l,{siteEnv:this.siteEnvVal},t=>{if(this.wait=!1,this.emitter.emit("overlay","hide"),t.data){this.setSiteEnv(this.siteEnvVal);let e={message:"실행환경이 변경 되었습니다"};this.emitter.emit("modalOpen",e)}}))}}},S=t=>(h("data-v-134df386"),t=t(),E(),t),T={class:"page-container"},w={class:"product-container"},g={class:"product-regist"},y=S(()=>s("div",{class:"product-title"},"실행 환경",-1)),I={class:"select-box"},x=f('<div class="sub-title" data-v-134df386>개발자 모드란?</div><ul class="tip-ul" data-v-134df386><li data-v-134df386> 사이트를 커스트마이징 하기 위해 설정합니다 </li><li data-v-134df386> VUE 파일을 수정할때 <span data-v-134df386>로컬에서 프록시로</span> 접근할때 보안을 회피할수 있습니다 </li><li data-v-134df386><span data-v-134df386>HTTP</span> 프로토콜을 사용할수 있습니다 </li></ul><div class="sub-title" style="margin-top:30px;" data-v-134df386>실서비스 모드란?</div><ul class="tip-ul" data-v-134df386><li data-v-134df386> 사이트를 실제 운영할때 사용합니다 </li><li data-v-134df386><span data-v-134df386>HTTPS</span> 프로토콜을 사용합니다 : 정부의 보안지침에 따라 개인정보 수집시 반드시 https 프로토콜을 사용<span data-v-134df386>(위반시 과태료가 발생합니다)</span> 해야 합니다 </li></ul>',4);function A(t,e,N,b,i,M){const o=r("v-select");return m(),_("div",T,[s("div",w,[s("div",g,[y,s("div",I,[u(o,{modelValue:i.siteEnvVal,"onUpdate:modelValue":e[0]||(e[0]=d=>i.siteEnvVal=d),density:"compact",variant:"outlined",items:i.sitenEnvList},null,8,["modelValue","items"])])]),x])])}const U=v(V,[["render",A],["__scopeId","data-v-134df386"]]);export{U as default};
