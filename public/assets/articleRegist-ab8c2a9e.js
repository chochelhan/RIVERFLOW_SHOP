import{e as q,a as x,a7 as T,a8 as N,a9 as E,aa as R}from"./apiService-6a3981ff.js";import{Q as C}from"./vue-quill.esm-bundler-54b853e3.js";import{J as j}from"./jquery-d0ecd42c.js";import{U as k}from"./util-c02dbcf9.js";import{m as M,_ as A,r as f,o as r,c,a as e,b as u,w as I,k as v,s as $,v as P,e as _,F,g as z,t as V,d as O,p as D,f as H,j as S}from"./index-7c0b344a.js";import{m as Q}from"./mobile-8dd88b05.js";import"./axios-c24e582b.js";const B={components:{QuillEditor:C},data(){return{bid:"",id:"",info:{},boardInfo:{},boardList:[],bidList:[],subject:"",content:"",editorOption:{placeholder:"내용을 입력하세요",theme:"snow",modules:{toolbar:{container:[["bold","italic","underline","strike"],[{color:[]},{background:[]}],[{align:[]}]]}}},btype:"",category:"",categoryList:[],categoryUse:"no",secretUse:"no",secret:"",bidDisabled:!1,imgs:[{isfile:"",file:null,url:""}],imgIndex:0,imgSize:{width:640,height:640,text:"640×640"},formHasErrors:!1,serverFlag:!1,boardName:"",memberInfo:{},isLogin:!1,wAuth:"user",userName:"",userPass:"",uploadImgType:"list",form:null}},created(){this.setClass("sub"),this.setTitle("글쓰기"),this.bid=this.$route.params.bid,this.$route.params.id!="regist"&&(this.id=this.$route.params.id);const i=q.getSession();i&&(i!=null&&i.memberInfo)&&(this.memberInfo=i.memberInfo,this.isLogin=!0),this.getData()},methods:{...M(Q,["setClass","setTitle"]),getData(){const i={bid:this.bid,id:this.id};x.get(T,i,s=>{var o,d,t,n,m,a,p,h,g;if(s.data){if(this.imgSize.width=(d=(o=s.data.imageset)==null?void 0:o.blist)!=null&&d.width?(n=(t=s.data.imageset)==null?void 0:t.blist)==null?void 0:n.width:640,this.imgSize.height=(a=(m=s.data.imageset)==null?void 0:m.blist)!=null&&a.height?(h=(p=s.data.imageset)==null?void 0:p.blist)==null?void 0:h.height:640,this.imgSize.text=this.imgSize.width+"×"+this.imgSize.height,(g=s.data.articleInfo)!=null&&g.id&&(this.info=s.data.articleInfo,this.subject=this.info.subject,this.$refs.myQuillEditor.setHTML(this.info.content)),this.boardInfo=s.data.boardInfo,this.boardInfo.wauth=="user"&&!this.isLogin){this.$router.back();return}this.wAuth=this.boardInfo.wauth,this.boardName=this.boardInfo.bname,this.setAddInfo()}})},setAddInfo(){if(this.boardInfo.categoryUse=="yes"){this.categoryUse="yes";for(let i of JSON.parse(this.boardInfo.categoryList))this.categoryList.push({text:i.name,value:i.code});this.info.category&&(this.category=this.info.category)}this.boardInfo.secret=="yes"&&(this.secretUse="yes",this.info.secret=="yes"&&(this.secret=!0)),this.wAuth=="all"&&!this.isLogin&&(this.userName=this.info.user_name),this.btype=this.boardInfo.btype,this.boardInfo.btype=="photo"&&this.info.img&&(this.imgs=[],this.imgs.push({isfile:"yes",file:null,url:this.info.img}))},editorImageHandler(){this.uploadImgType="contentEditor",this.$refs.img.click()},deleteImg(i){this.imgs.splice(i,1),this.imgs.push({isfile:"",file:null,url:""})},insertImg(i){this.uploadImgType="list",this.$refs.img.click()},imgUpload(){const i=this.$refs.img.files;let s=i.length>0?i[0]:null;switch(this.uploadImgType){case"contentEditor":const o=new FormData;o.append("image",s),x.postFile(N,o,t=>{const n=this.editor.getSelection();this.editor.insertEmbed(n.index,"image",t.data)});break;default:let d={isfile:"",file:s,url:URL.createObjectURL(s)};this.imgs.splice(0,1,d);break}this.$refs.img.value=""},validate(){var i,s;if(this.form){const o=new FormData,d=this.$refs.myQuillEditor.getHTML(),t=k.stripTags(d);if(!k.isImgTags(d)&&!t){let a={message:"내용을 입력하세요"};this.emitter.emit("modalOpen",a);return}if(o.append("subject",this.subject),o.append("category",this.category),o.append("user_name",this.userName),o.append("user_pass",this.userPass),o.append("bid",this.bid),o.append("content",d),o.append("secret",this.secret?"yes":"no"),o.append("btype",this.boardInfo.btype),this.btype=="photo"){if(this.imgs[0].file)o.append("img",this.imgs[0].file);else if(this.imgs[0].isfile!="yes"){let a={message:"이미지를 등록하세요"};this.emitter.emit("modalOpen",a);return}}if(this.serverFlag)return;this.serverFlag=!0,(i=this.info)!=null&&i.id&&o.append("id",this.info.id),this.emitter.emit("overlay","open");const m=(s=this.info)!=null&&s.id?E:R;x.postFile(m,o,a=>{var p;if(this.serverFlag=!1,this.emitter.emit("overlay","hide"),a.data){let h="저장";(p=this.info)!=null&&p.id&&(h="수정");let g={message:"게시글 정보가 "+h+" 되었습니다",doAction:()=>{this.$router.back()}};this.emitter.emit("modalOpen",g)}})}}},mounted(){j(".quill-content-box .ql-editor").css("min-height","400px")}},b=i=>(D("data-v-8343e296"),i=i(),H(),i),J={class:"page-container"},G={class:"product-container"},K={class:"product-regist"},W={class:"product-form",style:{"max-width":"100%"}},X={class:"input-ul"},Y={key:0},Z=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("작성자명")],-1)),ee={class:"input-box",id:"userName-required"},te={key:1},se=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("비밀번호")],-1)),ie={class:"input-box",id:"userPass-required"},oe={key:2},le=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("카테고리")],-1)),ne={class:"select-box",id:"category-required"},ae={key:3,style:{"margin-top":"40px"}},re=b(()=>e("div",{class:"label"},"비밀글 여부",-1)),de={class:"radio-box"},ce={style:{"font-weight":"bold",cursor:"pointer"}},ue={style:{"margin-top":"30px"}},me=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("제목")],-1)),he={class:"input-box",id:"subject-required"},pe=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("내용")],-1)),ge={class:"quill-content-box",style:{width:"100%","padding-bottom":"12px"}},fe={key:0,class:"product-form",style:{"margin-top":"40px"}},_e={class:"input-ul"},be=b(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("목록 이미지")],-1)),ye={class:"detail-img-box",style:{"padding-top":"5px"}},ve={class:"add-detial-img-ul"},xe={class:"reg-img-box"},Ie={key:0,class:"is-img"},we=["onClick"],ke=["src"],Le=["onClick"],Ue={class:"guide-img-icon"},Ve=b(()=>e("div",{class:"guide-img-title"},"이미지",-1)),Ae={class:"guide-img-size"},Se={class:"button-row",style:{display:"flex","justify-content":"space-between",width:"300px","max-width":"300px"}},qe=b(()=>e("span",null,"취소",-1)),Te=b(()=>e("span",null,"게시글 저장",-1));function Ne(i,s,o,d,t,n){const m=f("v-text-field"),a=f("v-select"),p=f("quill-editor"),h=f("font-awesome-icon"),g=f("v-btn"),L=f("v-form");return r(),c("div",J,[e("div",G,[u(L,{modelValue:t.form,"onUpdate:modelValue":s[7]||(s[7]=l=>t.form=l),novalidate:"",onSubmit:O(n.validate,["prevent"])},{default:I(()=>[e("input",{type:"file",style:{display:"none"},ref:"img",accept:"image/*",onChange:s[0]||(s[0]=(...l)=>n.imgUpload&&n.imgUpload(...l))},null,544),e("div",K,[e("div",W,[e("ul",X,[t.wAuth=="all"&&!t.isLogin?(r(),c("li",Y,[Z,e("div",ee,[u(m,{modelValue:t.userName,"onUpdate:modelValue":s[1]||(s[1]=l=>t.userName=l),rules:[()=>!!t.userName||"작성자명을 입력하세요"],variant:"outlined",rounded:"",ref:"userName",maxLength:"20"},null,8,["modelValue","rules"])])])):v("",!0),t.wAuth=="all"&&!t.isLogin?(r(),c("li",te,[se,e("div",ie,[u(m,{modelValue:t.userPass,"onUpdate:modelValue":s[2]||(s[2]=l=>t.userPass=l),rules:[()=>!!t.userPass||"비밀번호를 입력하세요"],variant:"outlined",ref:"userPass",type:"password",maxLength:"10",rounded:""},null,8,["modelValue","rules"])])])):v("",!0),t.categoryUse=="yes"?(r(),c("li",oe,[le,e("div",ne,[u(a,{modelValue:t.category,"onUpdate:modelValue":s[3]||(s[3]=l=>t.category=l),rounded:"",variant:"outlined",rules:[()=>!!t.category||"카테고리를 선택하세요"],ref:"category",label:"카테고리를 선택하세요",style:{"max-width":"500px"},items:t.categoryList},null,8,["modelValue","rules","items"])])])):v("",!0),t.secretUse=="yes"?(r(),c("li",ae,[re,e("div",de,[e("label",ce,[$(e("input",{type:"checkbox","onUpdate:modelValue":s[4]||(s[4]=l=>t.secret=l),value:"yes"},null,512),[[P,t.secret]]),_(" 비밀글 ")])])])):v("",!0),e("li",ue,[me,e("div",he,[u(m,{modelValue:t.subject,"onUpdate:modelValue":s[5]||(s[5]=l=>t.subject=l),rules:[()=>!!t.subject||"제목을 입력하세요"],variant:"outlined",ref:"subject",maxLength:"45",rounded:""},null,8,["modelValue","rules"])])]),e("li",null,[pe,e("div",ge,[u(p,{ref:"myQuillEditor",options:t.editorOption},null,8,["options"])])])])]),t.btype=="photo"?(r(),c("div",fe,[e("ul",_e,[e("li",null,[be,e("div",ye,[e("ul",ve,[(r(!0),c(F,null,z(t.imgs,(l,w)=>(r(),c("li",xe,[l.url?(r(),c("div",Ie,[e("div",{class:"is-img-close",onClick:U=>n.deleteImg(w)},[u(h,{icon:"fa-solid fa-xmark"})],8,we),e("img",{src:l.url},null,8,ke)])):(r(),c("div",{key:1,class:"guide-img",onClick:U=>n.insertImg(w)},[e("div",Ue,[u(h,{icon:"fa-solid fa-plus"})]),Ve,e("div",Ae,"권장 "+V(t.imgSize.text),1)],8,Le))]))),256))])])])])])):v("",!0)]),e("div",Se,[u(g,{style:{width:"48%","min-width":"48%","max-width":"48%"},class:"whiteButton",onClick:s[6]||(s[6]=l=>i.$router.back()),depressed:"",rounded:"",type:"button"},{default:I(()=>[qe]),_:1}),u(g,{style:{width:"48%","min-width":"48%","max-width":"48%"},class:"blackButton",depressed:"",rounded:"",type:"submit"},{default:I(()=>[Te]),_:1})])]),_:1},8,["modelValue","onSubmit"])])])}const Ee=A(B,[["render",Ne],["__scopeId","data-v-8343e296"]]);const Re={components:{QuillEditor:C},data(){return{bid:"",id:"",info:{},boardInfo:{},boardList:[],bidList:[],subject:"",content:"",editorOption:{placeholder:"내용을 입력하세요",theme:"snow",modules:{toolbar:{container:[[{header:1},{header:2}],["bold","italic","underline","strike"],["blockquote","code-block"],[{list:"ordered"},{list:"bullet"}],[{script:"sub"},{script:"super"}],[{indent:"-1"},{indent:"+1"}],[{direction:"rtl"}],[{size:["small",!1,"large","huge"]}],[{header:[1,2,3,4,5,6,!1]}],[{color:[]},{background:[]}],[{align:[]}],["image"],["clean"]],handlers:{image:this.editorImageHandler}}}},btype:"",category:"",categoryList:[],categoryUse:"no",secretUse:"no",secret:"",bidDisabled:!1,imgs:[{isfile:"",file:null,url:""}],imgIndex:0,imgSize:{width:640,height:640,text:"640×640"},formHasErrors:!1,serverFlag:!1,boardName:"",memberInfo:{},isLogin:!1,wAuth:"user",userName:"",userPass:"",uploadImgType:"list",editor:null,form:null}},created(){this.bid=this.$route.params.bid,this.$route.params.id!="regist"&&(this.id=this.$route.params.id);const i=q.getSession();i&&(i!=null&&i.memberInfo)&&(this.memberInfo=i.memberInfo,this.isLogin=!0),this.getData()},methods:{onEditorReady(i){this.editor=i},getData(){const i={bid:this.bid,id:this.id};x.get(T,i,s=>{var o,d,t,n,m,a,p,h,g;if(s.data){if(this.imgSize.width=(d=(o=s.data.imageset)==null?void 0:o.blist)!=null&&d.width?(n=(t=s.data.imageset)==null?void 0:t.blist)==null?void 0:n.width:640,this.imgSize.height=(a=(m=s.data.imageset)==null?void 0:m.blist)!=null&&a.height?(h=(p=s.data.imageset)==null?void 0:p.blist)==null?void 0:h.height:640,this.imgSize.text=this.imgSize.width+"×"+this.imgSize.height,(g=s.data.articleInfo)!=null&&g.id&&(this.info=s.data.articleInfo,this.subject=this.info.subject,this.$refs.myQuillEditor.setHTML(this.info.content)),this.boardInfo=s.data.boardInfo,this.boardInfo.wauth=="user"&&!this.isLogin){this.$router.back();return}this.wAuth=this.boardInfo.wauth,this.boardName=this.boardInfo.bname,this.setAddInfo()}})},setAddInfo(){if(this.boardInfo.categoryUse=="yes"){this.categoryUse="yes";for(let i of JSON.parse(this.boardInfo.categoryList))this.categoryList.push({text:i.name,value:i.code});this.info.category&&(this.category=this.info.category)}this.boardInfo.secret=="yes"&&(this.secretUse="yes",this.info.secret=="yes"&&(this.secret=!0)),this.wAuth=="all"&&!this.isLogin&&(this.userName=this.info.user_name),this.btype=this.boardInfo.btype,this.boardInfo.btype=="photo"&&this.info.img&&(this.imgs=[],this.imgs.push({isfile:"yes",file:null,url:this.info.img}))},editorImageHandler(){this.uploadImgType="contentEditor",this.$refs.img.click()},deleteImg(i){this.imgs.splice(i,1),this.imgs.push({isfile:"",file:null,url:""})},insertImg(i){this.uploadImgType="list",this.$refs.img.click()},imgUpload(){const i=this.$refs.img.files;let s=i.length>0?i[0]:null;switch(this.uploadImgType){case"contentEditor":const o=new FormData;o.append("image",s),x.postFile(N,o,t=>{const n=this.editor.getSelection();this.editor.insertEmbed(n.index,"image",t.data)});break;default:let d={isfile:"",file:s,url:URL.createObjectURL(s)};this.imgs.splice(0,1,d);break}this.$refs.img.value=""},onEditorChange({quill:i,html:s,text:o}){this.content=s},validate(){var i,s;if(this.form){const o=new FormData,d=this.$refs.myQuillEditor.getHTML(),t=k.stripTags(d);if(!k.isImgTags(d)&&!t){let a={message:"내용을 입력하세요"};this.emitter.emit("modalOpen",a);return}if(o.append("subject",this.subject),o.append("category",this.category),o.append("user_name",this.userName),o.append("user_pass",this.userPass),o.append("bid",this.bid),o.append("content",d),o.append("secret",this.secret?"yes":"no"),o.append("btype",this.boardInfo.btype),this.btype=="photo"){if(this.imgs[0].file)o.append("img",this.imgs[0].file);else if(this.imgs[0].isfile!="yes"){let a={message:"이미지를 등록하세요"};this.emitter.emit("modalOpen",a);return}}if(this.serverFlag)return;this.serverFlag=!0,(i=this.info)!=null&&i.id&&o.append("id",this.info.id),this.emitter.emit("overlay","open");const m=(s=this.info)!=null&&s.id?E:R;x.postFile(m,o,a=>{var p;if(this.serverFlag=!1,this.emitter.emit("overlay","hide"),a.data){let h="저장";(p=this.info)!=null&&p.id&&(h="수정");let g={message:"게시글 정보가 "+h+" 되었습니다",doAction:()=>{this.$router.back()}};this.emitter.emit("modalOpen",g)}})}}},mounted(){j(".quill-content-box .ql-editor").css("min-height","400px")}},y=i=>(D("data-v-e51056fb"),i=i(),H(),i),Ce={class:"page-container"},je={class:"product-container"},$e={class:"board-title"},Pe={class:"product-regist"},Fe={class:"product-form",style:{"max-width":"100%"}},ze={class:"input-ul"},Oe={key:0,class:"flex-box"},De={class:"left"},He=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("작성자명")],-1)),Me={class:"input-box",style:{width:"350px"},id:"userName-required"},Qe={class:"right"},Be=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("비밀번호")],-1)),Je={class:"input-box",id:"userPass-required",style:{width:"350px"}},Ge={key:1},Ke=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("카테고리")],-1)),We={class:"select-box",id:"category-required"},Xe={key:2,style:{"margin-top":"40px"}},Ye=y(()=>e("div",{class:"label"},"비밀글 여부",-1)),Ze={class:"radio-box",style:{"max-height":"40px"}},et={style:{"font-weight":"bold",cursor:"pointer"}},tt={style:{"margin-top":"30px"}},st=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("제목")],-1)),it={class:"input-box",id:"subject-required"},ot=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("내용")],-1)),lt={class:"quill-content-box",style:{width:"100%","padding-bottom":"12px"}},nt={key:0,class:"product-form",style:{"margin-top":"40px"}},at={class:"input-ul"},rt=y(()=>e("div",{class:"label"},[e("span",{class:"required-icon"},"*"),_("목록 이미지")],-1)),dt={class:"detail-img-box",style:{"padding-top":"5px"}},ct={class:"add-detial-img-ul"},ut={class:"reg-img-box"},mt={key:0,class:"is-img"},ht=["onClick"],pt=["src"],gt=["onClick"],ft={class:"guide-img-icon"},_t=y(()=>e("div",{class:"guide-img-title"},"이미지",-1)),bt={class:"guide-img-size"},yt={class:"button-row"},vt=y(()=>e("span",null,"취소",-1)),xt=y(()=>e("span",null,"게시글 저장",-1));function It(i,s,o,d,t,n){const m=f("v-text-field"),a=f("v-select"),p=f("quill-editor"),h=f("font-awesome-icon"),g=f("v-btn"),L=f("v-form");return r(),c("div",Ce,[e("div",je,[e("div",$e,V(t.boardName)+" 글쓰기",1),u(L,{modelValue:t.form,"onUpdate:modelValue":s[7]||(s[7]=l=>t.form=l),novalidate:"",onSubmit:O(n.validate,["prevent"])},{default:I(()=>[e("input",{type:"file",style:{display:"none"},ref:"img",accept:"image/*",onChange:s[0]||(s[0]=(...l)=>n.imgUpload&&n.imgUpload(...l))},null,544),e("div",Pe,[e("div",Fe,[e("ul",ze,[t.wAuth=="all"&&!t.isLogin?(r(),c("li",Oe,[e("div",De,[He,e("div",Me,[u(m,{modelValue:t.userName,"onUpdate:modelValue":s[1]||(s[1]=l=>t.userName=l),rules:[()=>!!t.userName||"작성자명을 입력하세요"],density:"compact",variant:"outlined",rounded:"",ref:"userName",maxLength:"20"},null,8,["modelValue","rules"])])]),e("div",Qe,[Be,e("div",Je,[u(m,{modelValue:t.userPass,"onUpdate:modelValue":s[2]||(s[2]=l=>t.userPass=l),rules:[()=>!!t.userPass||"비밀번호를 입력하세요"],density:"compact",variant:"outlined",ref:"userPass",type:"password",maxLength:"10",rounded:""},null,8,["modelValue","rules"])])])])):v("",!0),t.categoryUse=="yes"?(r(),c("li",Ge,[Ke,e("div",We,[u(a,{modelValue:t.category,"onUpdate:modelValue":s[3]||(s[3]=l=>t.category=l),rounded:"",rules:[()=>!!t.category||"카테고리를 선택하세요"],density:"compact",variant:"outlined",ref:"category",label:"카테고리를 선택하세요",style:{"max-width":"500px"},items:t.categoryList},null,8,["modelValue","rules","items"])])])):v("",!0),t.secretUse=="yes"?(r(),c("li",Xe,[Ye,e("div",Ze,[e("label",et,[$(e("input",{type:"checkbox","onUpdate:modelValue":s[4]||(s[4]=l=>t.secret=l),value:"yes"},null,512),[[P,t.secret]]),_(" 비밀글 ")])])])):v("",!0),e("li",tt,[st,e("div",it,[u(m,{modelValue:t.subject,"onUpdate:modelValue":s[5]||(s[5]=l=>t.subject=l),rules:[()=>!!t.subject||"제목을 입력하세요"],variant:"outlined",ref:"subject",maxLength:"45",rounded:""},null,8,["modelValue","rules"])])]),e("li",null,[ot,e("div",lt,[u(p,{ref:"myQuillEditor",onReady:n.onEditorReady,options:t.editorOption},null,8,["onReady","options"])])])])]),t.btype=="photo"?(r(),c("div",nt,[e("ul",at,[e("li",null,[rt,e("div",dt,[e("ul",ct,[(r(!0),c(F,null,z(t.imgs,(l,w)=>(r(),c("li",ut,[l.url?(r(),c("div",mt,[e("div",{class:"is-img-close",onClick:U=>n.deleteImg(w)},[u(h,{icon:"fa-solid fa-xmark"})],8,ht),e("img",{src:l.url},null,8,pt)])):(r(),c("div",{key:1,class:"guide-img",onClick:U=>n.insertImg(w)},[e("div",ft,[u(h,{icon:"fa-solid fa-plus"})]),_t,e("div",bt,"권장 "+V(t.imgSize.text),1)],8,gt))]))),256))])])])])])):v("",!0)]),e("div",yt,[u(g,{style:{width:"250px",margin:"auto","margin-right":"20px"},class:"whiteButton",onClick:s[6]||(s[6]=l=>i.$router.back()),depressed:"",rounded:"",type:"button"},{default:I(()=>[vt]),_:1}),u(g,{style:{width:"250px",margin:"auto"},class:"blackButton",depressed:"",rounded:"",type:"submit"},{default:I(()=>[xt]),_:1})])]),_:1},8,["modelValue","onSubmit"])])])}const wt=A(Re,[["render",It],["__scopeId","data-v-e51056fb"]]);const kt={components:{mobileArticleRegist:Ee,pcArticleRegist:wt},data(){return{mobile:!1}},created(){this.mobile=this.$isMobile()}},Lt={class:"page-container"};function Ut(i,s,o,d,t,n){const m=f("mobileArticleRegist"),a=f("pcArticleRegist");return r(),c("div",Lt,[t.mobile?(r(),S(m,{key:0})):(r(),S(a,{key:1}))])}const Rt=A(kt,[["render",Ut],["__scopeId","data-v-bb08c7df"]]);export{Rt as default};
