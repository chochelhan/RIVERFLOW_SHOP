import{a as y,ab as M,ac as N}from"./apiService-0aea4262.js";import{J as V}from"./jquery-331624d0.js";import{U as w}from"./util-5fdb7bcf.js";import{Q as j}from"./vue-quill.esm-bundler-7d17deec.js";import{_ as R,r as u,o as a,c,a as e,n as S,F as I,b,t as g,d as l,w as h,u as O,e as x,g as p,j as k,q as E,W as T,S as F,p as Q,f as U}from"./index-e9ed42ea.js";import"./axios-c24e582b.js";const q={components:{QuillEditor:j},data(){return{form:null,baseActive:"active",smsId:"base",smsSetList:[{name:"회원가입",id:"join",use:"no"},{name:"회원가입 인증문자",id:"joinAuth",use:"no"},{name:"상품구매 미입금(무통장)",id:"notpay",use:"no"},{name:"상품구매 입금완료(무통장)",id:"income",use:"no"},{name:"상품발송",id:"delivery",use:"no"},{name:"주문취소완료",id:"CC",use:"no"},{name:"반품완료",id:"RC",use:"no"},{name:"교환완료",id:"EC",use:"no"}],replaceOrgList:{join:[{name:"회원이름",code:"userName"},{name:"회원아이디",code:"userId"},{name:"회원가입일",code:"regDate"}],joinAuth:[{name:"인증번호",code:"authNumber"}],notpay:[{name:"회원이름",code:"userName"},{name:"회원아이디",code:"userId"},{name:"구매일",code:"orderDate"},{name:"구매정보",code:"orderInfo"},{name:"결제정보",code:"paymentInfo"},{name:"계좌정보",code:"accountInfo"},{name:"입금만료일",code:"bankExpire"}],buy:[{name:"회원이름",code:"userName"},{name:"회원아이디",code:"userId"},{name:"구매일",code:"orderDate"},{name:"구매정보",code:"orderInfo"},{name:"결제정보",code:"paymentInfo"}],income:[{name:"회원이름",code:"userName"},{name:"구매일",code:"orderDate"},{name:"구매정보",code:"orderInfo"},{name:"결제정보",code:"paymentInfo"}],delivery:[{name:"회원이름",code:"userName"},{name:"구매일",code:"orderDate"},{name:"구매정보",code:"orderInfo"},{name:"결제정보",code:"paymentInfo"}],CC:[{name:"회원이름",code:"userName"},{name:"취소상품정보",code:"cancleProduct"},{name:"환불금 정보",code:"refundPrice"}],RC:[{name:"회원이름",code:"userName"},{name:"반품상품정보",code:"returnProduct"},{name:"환불금정보",code:"refundPrice"}],EC:[{name:"회원이름",code:"userName"},{name:"교환상품정보",code:"exchangeProduct"}]},smsTitle:"SMS API 정보설정",serverFlag:!1,cafeSmsId:"",authkey:"",sendPcs:"",content:"",guse:"no",refCheck:!1,replaceItemShow:!1,replaceItemList:[],joinAuthReadOnly:!1,authTypePcs:!1,smsDataList:{},editor:null,editorOption:{placeholder:"문자 내용을 입력하세요",theme:"snow",modules:{toolbar:{container:[]}}}}},created(){this.getData()},methods:{onEditorReady(s){this.editor=s},cafeSmsIdErrors(s){return s?!0:"카페24 SMS 아이디를 입력하세요"},authkeyErrors(s){return s?!0:"카페24 인증키를 입력하세요"},sendPcsErrors(s){return s?!0:"발신 번호를 입력하세요"},initReplaceItem(){this.replaceItemList=this.replaceOrgList[this.smsId]},replaceTextOpen(){this.replaceItemShow=!0},setReplaceItem(s){const t=this.editor.getSelection();t?this.editor.insertText(t.index,"{{"+s+"}}"):this.editor.insertText(0,"{{"+s+"}}")},getData(){this.serverFlag||(this.serverFlag=!0,y.post(M,{gtype:"sms"},s=>{if(this.serverFlag=!1,s.data.settingInfo){for(const t of s.data.settingInfo){const n=t.content?JSON.parse(t.content):"";t.gid=="base"&&n?(this.cafeSmsId=n.smsId,this.authkey=n.authkey,this.sendPcs=n.sendPcs):t.gid!="base"&&(this.smsDataList[t.gid]={guse:t.guse,content:n?n.content:""})}for(let t of this.smsSetList)this.smsDataList[t.id]&&(t.use=this.smsDataList[t.id].guse)}if(s.data.joinSetting&&JSON.parse(s.data.joinSetting.member).authType=="pcs"){this.smsDataList.joinAuth.guse="yes",this.authTypePcs=!0;for(let n of this.smsSetList)n.id=="joinAuth"&&(n.use="yes")}}))},choiceMenu(s){let t={};for(let n of this.smsSetList)n.id==s?(n.active="active",t=n):n.active="";if(this.replaceItemShow=!1,this.smsId=s,s=="base")this.baseActive="active",this.smsTitle="SMS API 정보설정";else{this.initReplaceItem();let n="";if(this.smsDataList[this.smsId]){const d=this.smsDataList[this.smsId];n=d.content,this.guse=d.guse,this.subject?this.refCheck=!1:this.refCheck=!0}else this.guse="no",this.refCheck=!0;this.$refs.myQuillEditor?this.$refs.myQuillEditor.setText(n):setTimeout(()=>{this.$refs.myQuillEditor.setText(n)},300),this.joinAuthReadOnly=!1,this.authTypePcs&&this.smsId=="joinAuth"&&(this.joinAuthReadOnly=!0),this.baseActive="",this.smsTitle=t.name}},onEditorChange({quill:s,html:t,text:n}){this.content=t},validate(){if(this.serverFlag)return;let s={},t="";if(this.smsId=="base")if(this.form)s={smsId:this.cafeSmsId,authkey:this.authkey,sendPcs:this.sendPcs};else return;else{const n=this.$refs.myQuillEditor.getHTML(),d=w.stripTags(n);if(d==null||!d||d==" "||d==""){let i={message:"문자 내용을 입력하세요"};this.emitter.emit("modalOpen",i);return}t=this.$refs.myQuillEditor.getText(),s={guse:this.guse,content:t}}s.gid=this.smsId,s.gtype="sms",this.serverFlag=!0,this.emitter.emit("overlay","open"),y.post(N,s,n=>{this.serverFlag=!1,this.emitter.emit("overlay","hide"),this.smsDataList[this.smsId]={guse:this.guse,content:t};for(let i of this.smsSetList)if(i.id==this.smsId){i.use=this.guse;break}let d={message:"정보가 저장 되었습니다"};this.emitter.emit("modalOpen",d)})}},updated(){V(".quill-content-box .ql-editor").css("min-height","300px")}},m=s=>(Q("data-v-7c1a1d4d"),s=s(),U(),s),B={class:"page-container"},J={class:"imageSet-container"},G={class:"imageSet-list"},H=m(()=>e("div",{class:"imageSet-list-header"},[e("div",{class:"imageSet-title",style:{padding:"3px 0 0 0"}},"기본 정보")],-1)),z={class:"imageSet-list-body",style:{"min-height":"30px",height:"70px"}},W={class:"main-imageSet"},K={class:"main-li"},X=m(()=>e("div",{class:"gname"}," SMS API 정보설정 ",-1)),Y=[X],Z=m(()=>e("div",{class:"imageSet-list-header",style:{"border-top":"solid 1px #ccc"}},[e("div",{class:"imageSet-title",style:{padding:"3px 0 0 0"}},"문자 템플릿")],-1)),$={class:"imageSet-list-body"},ee={class:"main-imageSet"},te={class:"main-li"},se=["onClick"],ie={class:"gname"},oe={class:"imageSet-regist"},ne={key:0},ae={class:"imageSet-title"},le=m(()=>e("div",{style:{color:"#880E4F"}},"카페24 SMS 호스팅만을 사용합니다",-1)),de={key:0},re={class:"table-ul"},ce={class:"label-input"},me={class:"input-box"},ue={class:"label-input"},he={class:"input-box"},pe={class:"label-input"},_e={class:"input-box"},fe={key:1},ve={class:"table-ul"},ge=m(()=>e("div",{class:"label"}," 사용여부 ",-1)),ye={class:"label-input"},Se={class:"radio-box"},Ie=["readonly"],be=["readonly"],xe={class:"quill-content-box",style:{width:"300px","padding-bottom":"12px"}},ke={class:"replace-text-box"},Ee=m(()=>e("span",null,"치환정보 삽입",-1)),Te=m(()=>e("span",null," 회원정보, 구매정보 등을 실제내용에 맞게 메일내용에 삽입할수 있습니다 ",-1)),Ae={key:0,class:"replace-item-box"},Ce={class:"replace-item-header"},Le={class:"replace-item-body"},Pe={class:"button-row"};function De(s,t,n,d,i,r){const _=u("font-awesome-icon"),f=u("v-text-field"),A=u("v-tooltip"),v=u("v-btn"),C=u("quill-editor"),L=u("v-form");return a(),c("div",B,[e("div",J,[e("div",G,[H,e("div",z,[e("ul",W,[e("li",K,[e("div",{class:S("main-data "+i.baseActive),onClick:t[0]||(t[0]=o=>r.choiceMenu("base"))},Y,2)])])]),Z,e("div",$,[(a(!0),c(I,null,b(i.smsSetList,(o,P)=>(a(),c("ul",ee,[e("li",te,[e("div",{class:S("main-data "+o.active),onClick:D=>r.choiceMenu(o.id)},[e("div",ie,[p(g(o.name)+" ",1),o.use=="yes"?(a(),k(_,{key:0,icon:"fa-solid fa-eye"})):(a(),k(_,{key:1,icon:"fa-solid fa-eye-slash"}))])],10,se)])]))),256))])]),e("div",oe,[i.smsId?(a(),c("div",ne,[e("div",ae,g(i.smsTitle),1),le,l(L,{modelValue:i.form,"onUpdate:modelValue":t[8]||(t[8]=o=>i.form=o),novalidate:"",onSubmit:O(r.validate,["prevent"])},{default:h(()=>[i.smsId=="base"?(a(),c("div",de,[e("ul",re,[e("li",null,[e("div",ce,[e("div",me,[l(f,{modelValue:i.cafeSmsId,"onUpdate:modelValue":t[1]||(t[1]=o=>i.cafeSmsId=o),rules:[r.cafeSmsIdErrors],density:"compact",variant:"outlined",label:"카페24 SMS 아이디",placeholder:"카페24 SMS 아이디를 입력해주세요"},null,8,["modelValue","rules"])])])]),e("li",null,[e("div",ue,[e("div",he,[l(f,{modelValue:i.authkey,"onUpdate:modelValue":t[2]||(t[2]=o=>i.authkey=o),rules:[r.authkeyErrors],label:"카페24 인증키",density:"compact",variant:"outlined",placeholder:"카페24 인증키를 입력해주세요"},null,8,["modelValue","rules"])])])]),e("li",null,[e("div",pe,[e("div",_e,[l(f,{modelValue:i.sendPcs,"onUpdate:modelValue":t[3]||(t[3]=o=>i.sendPcs=o),rules:[r.sendPcsErrors],density:"compact",variant:"outlined",label:"발신 번호",placeholder:"발신 번호를 입력해주세요"},null,8,["modelValue","rules"])])])])])])):(a(),c("div",fe,[e("ul",ve,[e("li",null,[ge,e("div",ye,[e("div",Se,[e("label",null,[E(e("input",{type:"radio","onUpdate:modelValue":t[4]||(t[4]=o=>i.guse=o),readonly:i.joinAuthReadOnly,value:"yes"},null,8,Ie),[[T,i.guse]]),p(" 사용 ")]),e("label",null,[E(e("input",{type:"radio","onUpdate:modelValue":t[5]||(t[5]=o=>i.guse=o),readonly:i.joinAuthReadOnly,value:"no"},null,8,be),[[T,i.guse]]),p(" 사용안함 ")])])])])]),e("div",xe,[e("div",ke,[l(v,{onClick:t[6]||(t[6]=o=>r.replaceTextOpen()),class:"white-button",variant:"outlined",rounded:""},{default:h(()=>[Ee,l(A,{bottom:""},{activator:h(({props:o})=>[l(_,F(o,{class:"help-icon",icon:"fa-solid fa-circle-question"}),null,16)]),default:h(()=>[Te]),_:1})]),_:1}),i.replaceItemShow?(a(),c("div",Ae,[e("div",Ce,[l(_,{class:"icon",onClick:t[7]||(t[7]=o=>i.replaceItemShow=!1),icon:"fa-solid fa-xmark"})]),e("div",Le,[e("ul",null,[(a(!0),c(I,null,b(i.replaceItemList,(o,P)=>(a(),c("li",null,[l(v,{class:"item-btn",onClick:D=>r.setReplaceItem(o.code),density:"compact",variant:"outlined",color:"#01579B"},{default:h(()=>[p(g(o.name),1)]),_:2},1032,["onClick"])]))),256))])])])):x("",!0)]),l(C,{ref:"myQuillEditor",onReady:r.onEditorReady,options:i.editorOption},null,8,["onReady","options"])])])),e("div",Pe,[l(v,{class:"submit-button",style:{width:"250px"},type:"submit"},{default:h(()=>[p("정보 저장")]),_:1})])]),_:1},8,["modelValue","onSubmit"])])):x("",!0)])])])}const Oe=R(q,[["render",De],["__scopeId","data-v-7c1a1d4d"]]);export{Oe as default};
