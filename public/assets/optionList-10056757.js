import{a as _,aA as N,aB as D,ay as T}from"./apiService-0aea4262.js";import{U as y}from"./util-c02dbcf9.js";import{s as U}from"./shopTable-08e3e8cc.js";import{i as P}from"./inventoryHistory-9a7acc9b.js";import{_ as A,r as c,o as L,c as x,a as s,d as w,s as v,v as g,e as h,b as d,w as b,t as B,k as C,p as E,f as O}from"./index-7c0b344a.js";import"./axios-c24e582b.js";import"./jquery-d0ecd42c.js";const H={components:{shopTable:U,inventoryHistory:P},watch:{datalimit(a){this.getDataList(1)},cate1(a){this.subCategoryList=[];let t={title:"2차카테고리 선택",value:""};this.subCategoryList.push(t),this.subSubCategoryList=[];let l={title:"3차카테고리 선택",value:""};if(this.subSubCategoryList.push(l),this.cate2="",this.cate3="",this.orgSubCategoryList=[],a){for(let i of this.orgCategoryList)if(i.id==a&&i.subList&&i.subList.length>0)for(let e of i.subList)t={title:e.cname,value:e.id},this.subCategoryList.push(t),this.orgSubCategoryList.push(e)}},cate2(a){this.subSubCategoryList=[];let t={title:"3차카테고리 선택",value:""};if(this.subSubCategoryList.push(t),this.cate3="",a){for(let l of this.orgSubCategoryList)if(l.id==a&&l.subList&&l.subList.length>0)for(let i of l.subList)t={title:i.cname,value:i.id},this.subSubCategoryList.push(t)}}},data(){return{form:null,util:y,columns:[{name:"선택",id:"id",type:"allCheckbox",width:70},{name:"변경이력",id:"id",width:100,type:"button",action:"inventoryHistory"},{name:"이미지",id:"listImg",width:100,type:"image"},{name:"상품명",id:"pname",width:300,sort:!0},{name:"판매가능재고",id:"able_amt",width:120,sort:!0},{name:"판매불가재고",id:"disable_amt",width:120,sort:!0},{name:"총재고",id:"total_amt",width:100,sort:!0},{name:"판매된갯수",id:"sale_amt",width:110,sort:!0},{name:"판매상태",id:"pstatus",width:100},{name:"사용여부",id:"ouse",width:100},{name:"필수옵션",id:"orequiredName",width:100},{name:"카테고리",id:"category",width:300},{name:"브랜드",id:"brandId",width:200},{name:"정가",id:"price",width:100},{name:"할인가",id:"dcprice",width:100},{name:"등록일",id:"created_at",sort:!0,width:100}],fixed:9,productList:[],paging:{total:"",links:[],currentPage:1,lastPage:1},categoryListNames:{},categoryList:[],subCategoryList:[],subSubCategoryList:[],orgCategoryList:[],orgSubCategoryList:[],cate1:"",cate2:"",cate3:"",brandList:[],brandListNames:{},page:1,datalimit:20,pstatusList:{sale:"정상판매",hidden:"노출금지",soldout:"품절"},serviceTypeList:{normal:"배송상품군",service:"서비스상품군"},yesNoList:{yes:"가능",no:"불가능"},platform:[],pstatus:[],keywordCmd:"pname",keywordCmdList:[{title:"상품명",value:"pname"},{title:"키워드",value:"keyword"}],keyword:"",brand:"",stdate:"",endate:"",vmenu1:!1,vmenu2:!1,dateCmd:"created_at",dateCmdList:[{title:"상품등록일",value:"created_at"},{title:"판매시작일",value:"periodStdate"},{title:"판매종료일",value:"periodEndate"}],datalimitList:[{title:"10개",value:10},{title:"20개",value:20},{title:"30개",value:30},{title:"50개",value:50},{title:"100개",value:100}],ivtId:"",stickyTop:0,dataSort:{field:"id",sort:"desc"},inventoryDialog:!1,productInfo:{},type:"up",typeList:[{title:"입고",value:"up"},{title:"출고",value:"down"}],amt:"",updateBoxShow:!1,ids:[],formHasErrors:!1}},created(){this.getData(1)},methods:{searchReset(){this.keywordCmd="pname",this.keyword="",this.brand="",this.stdate="",this.endate="",this.dateCmd="created_at",this.cate1="",this.cate2="",this.cate3="",this.platform=[],this.pstatus=[]},getSearchParams(a){let t=[];this.cate1&&t.push(this.cate1),this.cate2&&t.push(this.cate2),this.cate3&&t.push(this.cate3);let l={orderByField:this.dataSort.field,orderBySort:this.dataSort.sort,limit:this.datalimit,page:a,keywordCmd:this.keywordCmd,keyword:this.keyword,brand:this.brand,stdate:this.stdate,endate:this.endate,dateCmd:this.dateCmd,category:t.length>0?t.join(","):""};return this.platform.length>0&&(l.platform=this.platform.join(",")),this.pstatus.length>0&&(l.pstatus=this.pstatus.join(",")),l},getData(a){const t=this.getSearchParams(a);_.post(N,t,l=>{this.orgCategoryList=l.data.categoryList;let i={title:"1차카테고리 선택",value:""};this.categoryList.push(i);let e={title:"2차카테고리 선택",value:""};this.subCategoryList.push(e);let r={title:"3차카테고리 선택",value:""};this.subSubCategoryList.push(r),this.categoryListNames={};for(let n of l.data.categoryList)if(i={title:n.cname,value:n.id},this.categoryList.push(i),this.categoryListNames[n.id]=n.cname,n.subList){for(let m of n.subList)if(this.categoryListNames[m.id]=m.cname,m.subList)for(let f of m.subList)this.categoryListNames[f.id]=f.cname}this.brandListNames={};let p={title:"브랜드 선택",value:""};this.brandList.push(p);for(let n of l.data.brandList){let m={title:n.bname,value:n.id};this.brandList.push(m),this.brandListNames[n.id]=n.bname}this.setDataList(l.data.productList)})},getDataList(a){const t=this.getSearchParams(a);_.post(D,t,l=>{this.setDataList(l.data)})},setDataList(a){this.productList=[],this.paging={total:a.total,links:a.links,currentPage:a.current_page,lastPage:a.last_page};const t=parseInt(a.total)-this.datalimit*(parseInt(a.current_page)-1);let l=0;for(let i of a.data){i.number=t-l,i.created_at=i.created_at.substring(0,10),i.able_amt?parseInt(i.able_amt)<1&&(i.pstatus="soldout"):i.pstatus="soldout",i.able_amt=y.numberFormat(i.able_amt),i.disable_amt=y.numberFormat(i.disable_amt),i.total_amt=y.numberFormat(i.total_amt),i.sale_amt=y.numberFormat(i.sale_amt),i.optionType=="single"?i.pname=i.pname+" ("+i.option_name+" : "+i.name+")":i.pname=i.pname+" ("+i.name+")",i.orequiredName=i.orequired=="Y"?"필수":"-";let e=i.category1.split(","),r=this.categoryListNames[e[0]];e[1]&&(r+=" > "+this.categoryListNames[e[1]]),e[2]&&(r+=" > "+this.categoryListNames[e[2]]),i.category=r,i.pstatus=this.pstatusList[i.pstatus],i.serviceType=this.serviceTypeList[i.serviceType],i.price=y.numberFormat(i.price),i.dcprice=y.numberFormat(i.dcprice),i.brandId=this.brandListNames[i.brandId],this.productList.push(i),l++}},pageMove(a){this.getDataList(a.page)},inventoryHistory(a){this.inventoryDialog=!0;for(const t of this.productList)if(t.id==a.id){this.ivtId=t.ivtId,this.productInfo={listImg:t.listImg,pname:t.pname,dcprice:t.dcprice,price:t.price,category:t.category};break}},search(){this.getDataList(1)},tableEventAction(a){switch(a.type){case"dataSort":this.dataSort=a.sort,this.getDataList(1);break}},updateAmt(){let a={};if(this.ids.length<1){a={message:"재고 변경할 상품을 하나 이상 선택하세요"},this.emitter.emit("modalOpen",a);return}this.form&&(a={type:"confirm",message:"선택된 상품의 재고를 변경하시겠습니까?",doAction:()=>{this.actionAmt()}},this.emitter.emit("modalOpen",a))},actionAmt(){const a={ids:this.ids,amt:this.amt,type:this.type};this.emitter.emit("overlay","open"),_.post(T,a,t=>{this.emitter.emit("overlay","hide");let l={message:"선택된 상품의 재고가 변경되었습니다"};this.amt="",this.type="up",this.updateBoxShow=!1,this.emitter.emit("modalOpen",l),this.getDataList(this.page)})},updateBoxToggle(){this.updateBoxShow?this.updateBoxShow=!1:this.updateBoxShow=!0},checkDataList(a){this.ids=[];let t={};for(let l in a)l&&(t[l]=l);for(let l of this.productList)t[l.id]&&l.ivtId&&this.ids.push(l.ivtId)}}},u=a=>(E("data-v-52379316"),a=a(),O(),a),F={class:"page-container"},M={class:"table-box"},R={class:"search-box"},j={class:"table-search",cellspacing:"0",cellpadding:"0",border:"0"},Y=u(()=>s("th",null,"판매상태",-1)),q={style:{display:"flex"}},z=u(()=>s("div",{style:{width:"20px"}},null,-1)),G=u(()=>s("div",{style:{width:"20px"}},null,-1)),J=u(()=>s("th",null,"플랫폼",-1)),K={style:{display:"flex"}},Q=u(()=>s("div",{style:{width:"20px"}},null,-1)),W=u(()=>s("th",null,"검색어",-1)),X={style:{display:"flex"}},Z=u(()=>s("th",null,"브랜드",-1)),$=u(()=>s("th",null,"카테고리",-1)),tt={colspan:"3"},et={style:{display:"flex"}},st=u(()=>s("div",{style:{width:"20px"}},null,-1)),it=u(()=>s("div",{style:{width:"20px"}},null,-1)),ot=u(()=>s("th",{style:{"border-radius":"5px"}},"기간검색",-1)),at={colspan:"3"},lt={style:{display:"flex"}},dt={style:{"margin-right":"20px"}},nt={style:{width:"160px"}},rt=u(()=>s("div",{style:{width:"30px","text-align":"center","line-height":"50px",height:"50px"}},"~ ",-1)),ut={style:{width:"160px"}},mt={class:"button-row"},pt=u(()=>s("span",null,"초기화",-1)),ht=u(()=>s("span",null,"검색",-1)),ct={style:{display:"flex",padding:"0 10px","justify-content":"space-between","z-index":"20"}},yt={class:"sub-title"},bt={class:"select-box",style:{display:"flex","max-height":"40px","padding-top":"4px"}},vt={class:"total"},gt={key:0,class:"status-change-box"},ft={class:"select-box"},_t={class:"number-box"},Lt={class:"input-box price",style:{width:"160px"}},xt={class:"button-box",style:{"max-width":"250px"}},wt={class:"modal-layout",style:{height:"760px"}},Ct={class:"modal-header"},Vt=u(()=>s("div",{class:"modal-title"},"재고 변경 이력",-1)),It={key:0,class:"modal-body",style:{height:"700px"}};function kt(a,t,l,i,e,r){const p=c("v-select"),n=c("v-text-field"),m=c("v-btn"),f=c("v-form"),V=c("shopTable"),I=c("font-awesome-icon"),k=c("inventoryHistory"),S=c("v-dialog");return L(),x("div",F,[s("div",M,[s("form",{novalidate:"",onSubmit:t[15]||(t[15]=w((...o)=>r.search&&r.search(...o),["prevent"]))},[s("div",R,[s("table",j,[s("tbody",null,[s("tr",null,[Y,s("td",null,[s("div",q,[s("label",null,[v(s("input",{type:"checkbox","onUpdate:modelValue":t[0]||(t[0]=o=>e.pstatus=o),value:"sale"},null,512),[[g,e.pstatus]]),h(" 정상판매 ")]),z,s("label",null,[v(s("input",{type:"checkbox","onUpdate:modelValue":t[1]||(t[1]=o=>e.pstatus=o),value:"hidden"},null,512),[[g,e.pstatus]]),h(" 노출중지 ")]),G,s("label",null,[v(s("input",{type:"checkbox","onUpdate:modelValue":t[2]||(t[2]=o=>e.pstatus=o),value:"soldout"},null,512),[[g,e.pstatus]]),h(" 품절 ")])])]),J,s("td",null,[s("div",K,[s("label",null,[v(s("input",{type:"checkbox","onUpdate:modelValue":t[3]||(t[3]=o=>e.platform=o),value:"pc"},null,512),[[g,e.platform]]),h(" PC ")]),Q,s("label",null,[v(s("input",{type:"checkbox","onUpdate:modelValue":t[4]||(t[4]=o=>e.platform=o),value:"mw"},null,512),[[g,e.platform]]),h(" 모바일 웹 ")])])])]),s("tr",null,[W,s("td",null,[s("div",X,[d(p,{modelValue:e.keywordCmd,"onUpdate:modelValue":t[5]||(t[5]=o=>e.keywordCmd=o),density:"compact",variant:"outlined",style:{"max-width":"150px"},items:e.keywordCmdList},null,8,["modelValue","items"]),d(n,{density:"compact",variant:"outlined",style:{width:"300px","margin-left":"20px"},modelValue:e.keyword,"onUpdate:modelValue":t[6]||(t[6]=o=>e.keyword=o)},null,8,["modelValue"])])]),Z,s("td",null,[d(p,{modelValue:e.brand,"onUpdate:modelValue":t[7]||(t[7]=o=>e.brand=o),density:"compact",variant:"outlined",style:{"max-width":"200px"},items:e.brandList},null,8,["modelValue","items"])])]),s("tr",null,[$,s("td",tt,[s("div",et,[d(p,{modelValue:e.cate1,"onUpdate:modelValue":t[8]||(t[8]=o=>e.cate1=o),density:"compact",variant:"outlined",style:{"max-width":"230px"},items:e.categoryList},null,8,["modelValue","items"]),st,d(p,{modelValue:e.cate2,"onUpdate:modelValue":t[9]||(t[9]=o=>e.cate2=o),density:"compact",variant:"outlined",style:{"max-width":"230px"},items:e.subCategoryList},null,8,["modelValue","items"]),it,d(p,{modelValue:e.cate3,"onUpdate:modelValue":t[10]||(t[10]=o=>e.cate3=o),density:"compact",variant:"outlined",style:{"max-width":"230px"},items:e.subSubCategoryList},null,8,["modelValue","items"])])])]),s("tr",null,[ot,s("td",at,[s("div",lt,[s("div",dt,[d(p,{modelValue:e.dateCmd,"onUpdate:modelValue":t[11]||(t[11]=o=>e.dateCmd=o),density:"compact",variant:"outlined",style:{"max-width":"150px"},items:e.dateCmdList},null,8,["modelValue","items"])]),s("div",nt,[d(n,{type:"date",modelValue:e.stdate,"onUpdate:modelValue":t[12]||(t[12]=o=>e.stdate=o),density:"compact",variant:"outlined"},null,8,["modelValue"])]),rt,s("div",ut,[d(n,{type:"date",density:"compact",variant:"outlined",modelValue:e.endate,"onUpdate:modelValue":t[13]||(t[13]=o=>e.endate=o)},null,8,["modelValue"])])])])])])])]),s("div",mt,[d(m,{class:"white-button",variant:"outlined",onClick:t[14]||(t[14]=o=>r.searchReset())},{default:b(()=>[pt]),_:1}),d(m,{class:"search-button",type:"submit",variant:"outlined"},{default:b(()=>[ht]),_:1})])],32),s("div",ct,[s("div",yt,[h(" 상품 리스트 "),d(m,{depressed:"",color:"primary",variant:"outlined",class:"sdel-button",rounded:"",onClick:t[16]||(t[16]=o=>r.updateBoxToggle())},{default:b(()=>[h(" 재고 변경 ")]),_:1})]),s("div",bt,[s("div",vt,"전체상품수 "+B(e.util.numberFormat(e.paging.total))+"개",1),d(p,{modelValue:e.datalimit,"onUpdate:modelValue":t[17]||(t[17]=o=>e.datalimit=o),density:"compact","bg-color":"#fff",style:{"max-width":"100px"},items:e.datalimitList},null,8,["modelValue","items"])])]),d(f,{modelValue:e.form,"onUpdate:modelValue":t[21]||(t[21]=o=>e.form=o),onSubmit:w(r.updateAmt,["prevent"])},{default:b(()=>[e.updateBoxShow?(L(),x("div",gt,[s("div",ft,[d(p,{modelValue:e.type,"onUpdate:modelValue":t[18]||(t[18]=o=>e.type=o),density:"compact",variant:"outlined",style:{width:"150px"},items:e.typeList},null,8,["modelValue","items"])]),s("div",_t,[s("div",Lt,[d(n,{density:"compact",variant:"outlined",modelValue:e.amt,"onUpdate:modelValue":t[19]||(t[19]=o=>e.amt=o),ref:"amt",suffix:"개",rules:[()=>!!e.amt||"변경재고를 입력하세요",()=>!isNaN(e.amt)||"재고는 숫자만 입력가능합니다"]},null,8,["modelValue","rules"])])]),s("div",xt,[d(m,{depressed:"",color:"#006064",variant:"outlined",class:"sdel-button",style:{width:"70px"},rounded:"",type:"submit"},{default:b(()=>[h("확인 ")]),_:1}),d(m,{depressed:"",color:"#880E4F",variant:"outlined",class:"sdel-button",type:"button",style:{width:"70px","margin-left":"10px"},rounded:"",onClick:t[20]||(t[20]=o=>e.updateBoxShow=!1)},{default:b(()=>[h("취소 ")]),_:1})])])):C("",!0)]),_:1},8,["modelValue","onSubmit"]),d(V,{datas:e.productList,updateStickyTop:e.stickyTop,fixed:e.fixed,onPageMove:r.pageMove,onParentEvent:r.tableEventAction,onInventoryHistory:r.inventoryHistory,columns:e.columns,paging:e.paging,onCheckDataList:r.checkDataList},null,8,["datas","updateStickyTop","fixed","onPageMove","onParentEvent","onInventoryHistory","columns","paging","onCheckDataList"])]),d(S,{modelValue:e.inventoryDialog,"onUpdate:modelValue":t[24]||(t[24]=o=>e.inventoryDialog=o),width:"800",persistent:"",rounded:"",style:{"z-index":"100"}},{default:b(()=>[s("div",wt,[s("div",Ct,[Vt,s("div",{class:"modal-close",onClick:t[22]||(t[22]=o=>e.inventoryDialog=!1)},[d(I,{icon:"fa-solid fa-circle-xmark"})])]),e.inventoryDialog?(L(),x("div",It,[d(k,{ivtId:e.ivtId,productInfo:e.productInfo,onInventoryDialogHide:t[23]||(t[23]=o=>e.inventoryDialog=!1)},null,8,["ivtId","productInfo"])])):C("",!0)])]),_:1},8,["modelValue"])])}const Bt=A(H,[["render",kt],["__scopeId","data-v-52379316"]]);export{Bt as default};
