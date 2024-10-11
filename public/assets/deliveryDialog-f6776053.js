import{a as u,x as p}from"./apiService-aee0d783.js";import{_ as v,r as g,o,c as i,g as d,a as t,F as m,b as f,t as l,d as y,w as x,p as S,f as b}from"./index-e9ed42ea.js";const k={props:["oid"],data(){return{serverState:"load",stateText:"",progress:[],carrierName:"",carrierTel:""}},created(){this.getData()},methods:{getData(){u.post(p,{oid:this.oid},s=>{if(s.status=="success")if(s.data=="direct")this.serverState="direct";else if(this.stateText=s.data.ostatus=="DI"?"배송중":"배송완료",this.carrierName=s.data.companyName,this.carrierTel=s.data.companyTel,s.data.stype=="tracker"){if(this.serverState="succ",s.data.trackerList)for(let e of s.data.trackerList)e.date&&(e.time=e.date,e.location=e.position,e.status=e.status,this.progress.push(e))}else{const e=JSON.parse(s.data.trackerList);e.state&&e.progresses?(this.serverState="succ",this.setData(e)):this.serverState="fail"}else{this.hide();let e={message:"배송정보가 존재하지 않습니다"};this.$eventBus.$emit("modalOpen",e)}})},setData(s){for(const e of s.progresses){const n={time:e.time.substring(0,16).replace("T"," "),location:e.location.name,status:e.status.text};this.progress.push(n)}},hide(){this.$emit("hideEvent",{})}}},r=s=>(S("data-v-55414a32"),s=s(),b(),s),D={class:"login-container"},T=r(()=>t("p",{class:"login-title"},"배송 조회",-1)),w={key:0,class:"gray-box"},N={key:1,class:"gray-box"},E=r(()=>t("br",null,null,-1)),I={key:2,class:"gray-box"},L={key:3},B={class:"w-full"},C={style:{width:"100%"}},R=r(()=>t("thead",null,[t("tr",null,[t("th",{style:{width:"150px"}},"일자"),t("th",null,"위치"),t("th",{style:{width:"350px"}},"배송상태")])],-1)),V={class:"carrier-box"},A=r(()=>t("div",{class:"gtitle"},"배송상태",-1)),F={class:"gvalue"},G=r(()=>t("div",{class:"gtitle"},"배송업체",-1)),O={class:"gvalue"},P=r(()=>t("div",{class:"gtitle"},"연락처",-1)),Y={class:"gvalue"},J={class:"button-row"},K=r(()=>t("span",null,"닫기",-1));function M(s,e,n,U,a,_){const h=g("v-btn");return o(),i("div",D,[T,a.serverState=="load"?(o(),i("div",w," 서버에서 배송 정보를 가져오는 중입니다 ")):a.serverState=="direct"?(o(),i("div",N,[d(" 죄송합니다"),E,d(" 직접배송 되는 주문은 배송조회 서비스를 제공하지 않습니다 ")])):a.serverState=="fail"?(o(),i("div",I," 잘못된 배송정보 입니다 ")):(o(),i("div",L,[t("div",B,[t("table",C,[R,t("tbody",null,[(o(!0),i(m,null,f(a.progress,c=>(o(),i("tr",null,[t("td",null,l(c.time),1),t("td",null,l(c.location),1),t("td",null,l(c.status),1)]))),256))])])]),t("ul",V,[t("li",null,[A,t("div",F,l(a.stateText),1)]),t("li",null,[G,t("div",O,l(a.carrierName),1)]),t("li",null,[P,t("div",Y,l(a.carrierTel),1)])])])),t("div",J,[y(h,{type:"button",depressed:"",rounded:"",onClick:e[0]||(e[0]=c=>_.hide()),class:"whiteButton"},{default:x(()=>[K]),_:1})])])}const z=v(k,[["render",M],["__scopeId","data-v-55414a32"]]);export{z as d};
