import{C as w,p as x,a as S,b as V,L as T,c as I,d as E,P as A,e as B}from"./index-8c66701e.js";import{a as G,b9 as F}from"./apiService-0aea4262.js";import"./jquery-331624d0.js";import{_ as P,r,o as h,c as _,a as l,d as i,w as c,n as u,j as f,g as m,p as N,f as U}from"./index-e9ed42ea.js";import"./axios-c24e582b.js";w.register(x,S,V,T,I,E,A);const O={components:{LineChartGenerator:B},watch:{cmd(s){this.setInit()}},data(){return{cmd:0,chartData:{labels:[],datasets:[]},chartOptions:{responsive:!0,maintainAspectRatio:!1},chartShow:!1,height:600,dateClass:{year:"",month:"",day:"active"},dateType:"day",month:"",yearList:[],styear:"",enyear:"",dayList:[],menu1:!1,label:"",colorList:["#C62828","#4A148C","#3949AB","#1E88E5","#00ACC1","#00897B","#43A047","#827717","#FFB300","#F4511E"]}},created(){this.setInit()},methods:{setInit(){const s=new Date,t=s.getFullYear();for(let n=t;n>=2010;n--)this.yearList.push({title:n+"년",value:n});this.styear=t-5,this.enyear=s.getFullYear();let a=parseInt(s.getMonth())+1;a<10&&(a="0"+a),this.month=t+"-"+a,this.getData()},getData(){const s=this.setDataParams();console.log(s),G.post(F,s,t=>{this.setGraph(t.data)})},setDataParams(){return this.chartShow=!1,this.setDateList(),{dateType:this.dateType,styear:this.styear,enyear:this.enyear,month:this.month,type:this.cmd<1?"price":"count"}},setDateList(){let s,t;switch(this.dateType){case"day":const a=this.month.split("-");s=new Date(a[0],a[1],0).getDate(),t=1,this.label=this.month+"월 주문 현황";break;case"month":s=12,t=1,this.styear==this.enyear?this.label=this.styear+"년 월별 주문 현황":this.label=this.styear+"년 ~ "+this.enyear+"년까지 월별 주문 현황";break;case"year":this.styear==this.enyear?this.label=this.styear+"년 주문 현황":this.label=this.styear+"년 ~ "+this.enyear+"년까지 연도별 주문 현황",s=this.enyear,t=this.styear;break}this.dayList=[];for(let a=t;a<=s;a++){let n=a<10?"0"+a:a;this.dayList.push(n)}},setDateType(s){this.dateType=s;for(const t in this.dateClass)t==s?this.dateClass[t]="active":this.dateClass[t]=""},search(){this.getData()},setGraph(s){this.chartData.labels=[];const t={};for(const e of s)t[e.dateGroup]=e.total;let a=[];for(const e of this.dayList)this.chartData.labels.push(e),t[e]?a.push(t[e]):a.push(0);this.chartData.datasets=[];const n={label:this.label,backgroundColor:"#f87979",data:a};this.chartData.datasets.push(n),this.chartShow=!0}}},y=s=>(N("data-v-50acb3a9"),s=s(),U(),s),R={class:"page-container"},z={class:"tab-box"},M=y(()=>l("span",{class:"bold"},"주문금액별 통계",-1)),Y=y(()=>l("span",{class:"bold"},"주문건수별 통계",-1)),j={class:"product-container"},K=y(()=>l("div",{class:"statis-title"},"[구매확정된 주문만 통계에 반영합니다]",-1)),q={style:{display:"flex"}},H={class:"date-box"},J={key:0,class:"month-box"},Q={key:1,class:"year-box"},W=y(()=>l("span",null,"-",-1)),X={class:"button-box"},Z={class:"graph-box"};function $(s,t,a,n,e,d){const b=r("v-tab"),g=r("v-tabs"),p=r("v-btn"),D=r("v-text-field"),v=r("v-select"),C=r("font-awesome-icon"),L=r("LineChartGenerator"),k=r("v-progress-circular");return h(),_("div",R,[l("div",z,[i(g,{modelValue:e.cmd,"onUpdate:modelValue":t[0]||(t[0]=o=>e.cmd=o)},{default:c(()=>[i(b,{class:"tab"},{default:c(()=>[M]),_:1}),i(b,{class:"tab"},{default:c(()=>[Y]),_:1})]),_:1},8,["modelValue"])]),l("div",j,[K,l("div",q,[l("div",H,[i(p,{variant:"outlined",class:u("white-button "+e.dateClass.year),onClick:t[1]||(t[1]=o=>d.setDateType("year"))},{default:c(()=>[m("년도별 ")]),_:1},8,["class"]),i(p,{variant:"outlined",class:u("white-button "+e.dateClass.month),onClick:t[2]||(t[2]=o=>d.setDateType("month"))},{default:c(()=>[m("월별 ")]),_:1},8,["class"]),i(p,{variant:"outlined",class:u("white-button "+e.dateClass.day),onClick:t[3]||(t[3]=o=>d.setDateType("day"))},{default:c(()=>[m(" 일별 ")]),_:1},8,["class"])]),e.dateClass.day=="active"?(h(),_("div",J,[i(D,{type:"date",density:"compact",variant:"outlined",modelValue:e.month,"onUpdate:modelValue":t[4]||(t[4]=o=>e.month=o)},null,8,["modelValue"])])):(h(),_("div",Q,[i(v,{class:"year-item",modelValue:e.styear,"onUpdate:modelValue":t[5]||(t[5]=o=>e.styear=o),density:"compact",variant:"outlined",items:e.yearList},null,8,["modelValue","items"]),W,i(v,{class:"year-item",density:"compact",variant:"outlined",modelValue:e.enyear,"onUpdate:modelValue":t[6]||(t[6]=o=>e.enyear=o),items:e.yearList},null,8,["modelValue","items"])])),l("div",X,[i(p,{variant:"outlined",class:"white-button submit-button",onClick:t[7]||(t[7]=o=>d.search())},{default:c(()=>[i(C,{icon:"fa-solid fa-magnifying-glass",style:{"margin-right":"6px"}}),m(" 검색 ")]),_:1})])]),l("div",Z,[e.chartShow?(h(),f(L,{key:0,options:e.chartOptions,data:e.chartData,height:e.height},null,8,["options","data","height"])):(h(),f(k,{key:1,size:100,class:"progress-box",color:"#880E4F",indeterminate:""}))])])])}const it=P(O,[["render",$],["__scopeId","data-v-50acb3a9"]]);export{it as default};
