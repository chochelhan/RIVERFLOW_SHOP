import{e as V,a as L,ab as B,ac as j,ad as M,ae as E,a7 as J,af as z}from"./apiService-6a3981ff.js";import{a as G}from"./articleUpass-b2e5ca65.js";import{J as D}from"./jquery-d0ecd42c.js";import{i as Q,m as x,_ as T,r as v,o as n,c as a,a as i,t as b,b as r,w as u,j as I,d as q,F as R,g as $,e as U,k as f,p as F,f as P}from"./index-7c0b344a.js";import{m as W}from"./mobile-8dd88b05.js";import{U as y}from"./util-c02dbcf9.js";import{c as O}from"./config-9369631b.js";import"./axios-c24e582b.js";const X={name:"pcCommentList",props:["auth","ptype","pid"],computed:{...Q(O,["listScrollMore"])},watch:{listScrollMore(t){t&&this.handleLoadMore()}},data(){return{mainForm:null,limit:10,page:1,lastPage:1,total:0,memberImg:"",isLogin:!1,commentList:[],comment:"",commentId:"",memberId:"",insertFlag:!1,formHasErrors:!1,subComment:[],subCommentError:[],subCommentUpdate:[],commentIndex:"",scrolling:!1,commentRepleShow:[]}},created(){const t=V.getSession();t&&(t!=null&&t.memberInfo)&&(this.memberId=t.memberInfo.id,this.isLogin=!0),this.getData()},methods:{...x(O,["setListScroll","setListScrollMore"]),getData(){this.scrolling=!0;const t=this.getSearchParams();L.get(B,t,e=>{var o;this.commentList=[],this.total=y.numberFormat(e.data.commentList.total),this.$emit("updateCommentCnt",{total:e.data.commentList.total}),this.setCommentList(e.data.commentList),this.isLogin&&(this.memberImg=(o=e.data.memberInfo)!=null&&o.img?e.data.memberInfo.img:"")})},getDataList(){this.scrolling=!0;const t=this.getSearchParams();L.get(B,t,e=>{this.setCommentList(e.data.commentList)})},setCommentList(t){this.lastPage=t.last_page;for(let e of t.data){e.viewName=e.memNick?e.memNick:e.memName,e.modifyButton=!!(this.isLogin&&this.memberId==e.user_id);for(let o of e.subList)o.viewName=o.memNick?o.memNick:o.memName,o.modifyButton=!!(this.isLogin&&this.memberId==o.user_id);this.commentList.push(e)}for(const e in this.commentList)this.subComment[e]="",this.subCommentError[e]="",this.subCommentUpdate[e]=!1,this.commentRepleShow[e]=!1;this.scrolling=!1,this.page<this.lastPage?(this.setListScroll(!0),this.setListScrollMore(!1)):this.setListScroll(!1)},getSearchParams(){return{parentType:this.ptype,parentId:this.pid,limit:this.limit,page:this.page}},setModify(t){if(this.subCommentId){const o={message:"기존 수정 작업을 먼저 완료하세요"};this.emitter.emit("modalOpen",o);return}const e=this.commentList[t];this.comment=y.stripTags(e.content),this.commentId=e.id},showSubComment(t){for(const e in this.commentRepleShow)this.commentRepleShow[e]=!1;this.commentRepleShow[t]=!0},setSubModify(t,e){if(this.commentId){const _={message:"기존 수정 작업을 먼저 완료하세요"};this.emitter.emit("modalOpen",_);return}const o=this.commentList[t].subList[e];this.subComment[t]=y.stripTags(o.content),this.subCommentId=o.id,this.commentIndex=t;for(const _ in this.subCommentUpdate)this.subCommentUpdate[_]=!1;this.subCommentUpdate[t]=!0,this.showSubComment(t)},deleteComment(t){const e={type:"confirm",message:"댓글을 삭제하시겠습니까?",doAction:()=>{const o=this.commentList[t].id;this.blindAction(o,"main")}};this.emitter.emit("modalOpen",e)},deleteSubComment(t,e){const o={type:"confirm",message:"답글을 삭제하시겠습니까?",doAction:()=>{const _=this.commentList[t].subList[e].id;this.blindAction(_,"sub")}};this.emitter.emit("modalOpen",o)},blindAction(t,e){const o={id:t};this.emitter.emit("overlay","open"),L.post(j,o,_=>{this.emitter.emit("overlay","hide"),this.page=1,this.getData();const m={message:(e=="sub"?"답글":"댓글")+"이 삭제되었습니다"};this.emitter.emit("modalOpen",m)})},reset(){this.commentId="",this.comment="",this.$refs.comment.reset()},subReset(t){this.subCommentId="";for(const e in this.subCommentUpdate)this.subCommentUpdate[e]=!1;this.subComment[t]="",this.subCommentError[t]=""},subCommentErrorCheck(t){this.subComment[t]?this.subCommentError[t]="":this.subCommentError[t]="답글을 입력하세요"},updateSubReview(t){if(this.insertFlag)return;if(this.auth=="user"&&!this.isLogin){let s={message:"먼저 로그인 하세요"};this.emitter.emit("modalOpen",s);return}if(!this.subComment[t]){this.subCommentError[t]="답글을 입력하세요";return}this.insertFlag=!0,this.emitter.emit("overlay","open");const e=this.commentList[t].id;let o="",_={parentId:this.pid,parentType:this.ptype,content:this.subComment[t],pid:e};this.subCommentId?(o=M,_.id=this.subCommentId):o=E,L.post(o,_,s=>{if(this.emitter.emit("overlay","hide"),this.insertFlag=!1,this.subCommentId){let p=0,d={};for(let k of this.commentList[this.commentIndex].subList){if(k.id==this.subCommentId){k.content=y.nl2brContent(this.subComment[t]),d=k;break}p++}this.commentList[this.commentIndex].subList[p]=d;for(const k in this.commentRepleShow)this.commentRepleShow[k]=!1}else this.page=1,this.getData();const h={message:"답글이 "+(this.subCommentId?"수정":"저장")+" 되었습니다"};this.subReset(this.commentIndex),this.commentIndex="",this.emitter.emit("modalOpen",h)})},updateReview(){if(this.insertFlag)return;if(this.auth=="user"&&!this.isLogin){let o={message:"먼저 로그인 하세요"};this.emitter.emit("modalOpen",o);return}if(!this.mainForm)return;this.insertFlag=!0,this.emitter.emit("overlay","open");let t="",e={parentId:this.pid,parentType:this.ptype,content:this.comment};this.commentId?(t=M,e.id=this.commentId):t=E,L.post(t,e,o=>{if(this.emitter.emit("overlay","hide"),this.insertFlag=!1,this.commentId){let m=0,h={};for(let p of this.commentList){if(p.id==this.commentId){p.content=y.nl2brContent(this.comment),h=p;break}m++}this.commentList[m]=h}else this.page=1,this.getData();const s={message:"댓글이 "+(this.commentId?"수정":"저장")+" 되었습니다"};this.commentId="",this.comment="",this.$refs.comment.reset(),this.emitter.emit("modalOpen",s)})},handleLoadMore(){this.page<this.lastPage&&(this.page++,this.getDataList())}},destroyed(){this.setListScroll(!1)}},N=t=>(F("data-v-0df0acad"),t=t(),P(),t),Y={class:"comment-container",id:"comment-base"},Z={class:"comment-title"},tt={class:"form-box"},et={class:"img-box"},st=["src"],it={class:"input-box",id:"c"},ot={key:0,class:"button-box"},nt={class:"comment-box"},mt={class:"main-comment-ul"},at=["id"],rt={class:"main-comment"},ct={class:"user-box"},lt={class:"img-box"},dt=["src"],ht={class:"uname"},ut={class:"content-box"},_t=["innerHTML"],pt={class:"rdate"},ft={class:"modify-button-box"},bt=N(()=>i("span",null,"답글달기",-1)),gt=N(()=>i("span",null,"삭제",-1)),vt=N(()=>i("span",null,"수정",-1)),yt={key:0,class:"sub-comment"},Ct={key:0,class:"sub-form-box"},It={class:"img-box"},kt=["src"],Lt={class:"input-box"},wt={key:0,class:"button-box"},St={key:1,class:"sub-comment-ul"},Ut=["id"],Tt={class:"user-box"},Nt={class:"img-box"},At=["src"],Bt={class:"uname"},Mt={class:"content-box"},Et=["innerHTML"],Dt={class:"rdate"},Rt={key:0,class:"modify-button-box"},$t=N(()=>i("span",null,"삭제",-1)),Ot=N(()=>i("span",null,"수정",-1)),Vt={key:0,style:{"text-align":"center",padding:"200px 0"}};function Ft(t,e,o,_,s,m){const h=v("font-awesome-icon"),p=v("v-text-field"),d=v("v-btn"),k=v("v-form"),w=v("v-progress-circular");return n(),a("div",Y,[i("div",Z,"댓글 ("+b(s.total)+")",1),r(k,{modelValue:s.mainForm,"onUpdate:modelValue":e[2]||(e[2]=C=>s.mainForm=C),onSubmit:q(m.updateReview,["prevent"])},{default:u(()=>[i("div",tt,[i("div",et,[s.memberImg?(n(),a("img",{key:0,src:s.memberImg},null,8,st)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",it,[r(p,{ref:"comment",modelValue:s.comment,"onUpdate:modelValue":e[0]||(e[0]=C=>s.comment=C),variant:"outlined",density:"compact",maxLength:"250",rules:[()=>!!s.comment||"댓글을 입력하세요"]},null,8,["modelValue","rules"])]),s.commentId?(n(),a("div",ot,[r(d,{class:"blackButton",type:"submit",rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-paper-plane"})]),_:1}),r(d,{class:"whiteButton",onClick:e[1]||(e[1]=C=>m.reset()),rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-ban"})]),_:1})])):(n(),I(d,{key:1,class:"blackButton",type:"submit",rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-paper-plane"})]),_:1}))])]),_:1},8,["modelValue","onSubmit"]),i("div",nt,[i("ul",mt,[(n(!0),a(R,null,$(s.commentList,(C,c)=>(n(),a("li",{key:C.id,id:"comment_"+C.id},[i("div",rt,[i("div",ct,[i("div",lt,[C.memImg?(n(),a("img",{key:0,src:C.memImg},null,8,dt)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",ht,b(C.viewName),1)]),i("div",ut,[i("div",{class:"content",innerHTML:C.content},null,8,_t),i("div",pt,[U(b(C.created_at.substring(0,10))+" ",1),i("div",ft,[r(d,{class:"whiteButton reple",onClick:l=>m.showSubComment(c),variant:"outlined",rounded:""},{default:u(()=>[bt]),_:2},1032,["onClick"]),C.modifyButton?(n(),I(d,{key:0,class:"whiteButton",onClick:l=>m.deleteComment(c),variant:"outlined",rounded:""},{default:u(()=>[gt]),_:2},1032,["onClick"])):f("",!0),C.modifyButton?(n(),I(d,{key:1,class:"blackButton",onClick:l=>m.setModify(c),variant:"outlined",rounded:""},{default:u(()=>[vt]),_:2},1032,["onClick"])):f("",!0)])])])]),C.subList.length>0||s.commentRepleShow[c]?(n(),a("div",yt,[s.commentRepleShow[c]?(n(),a("div",Ct,[i("div",It,[s.memberImg?(n(),a("img",{key:0,src:s.memberImg},null,8,kt)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",Lt,[r(p,{"error-messages":s.subCommentError[c],modelValue:s.subComment[c],"onUpdate:modelValue":l=>s.subComment[c]=l,variant:"outlined",density:"compact",maxLength:"150",onInput:l=>m.subCommentErrorCheck(c),onBlur:l=>m.subCommentErrorCheck(c)},null,8,["error-messages","modelValue","onUpdate:modelValue","onInput","onBlur"])]),s.subCommentUpdate[c]?(n(),a("div",wt,[r(d,{class:"blackButton",onClick:l=>m.updateSubReview(c),rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-paper-plane"})]),_:2},1032,["onClick"]),r(d,{class:"whiteButton",onClick:l=>m.subReset(c),rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-ban"})]),_:2},1032,["onClick"])])):(n(),I(d,{key:1,class:"blackButton",onClick:l=>m.updateSubReview(c),rounded:"",variant:"outlined"},{default:u(()=>[r(h,{class:"icon",icon:"fa-solid fa-paper-plane"})]),_:2},1032,["onClick"]))])):f("",!0),C.subList.length>0?(n(),a("ul",St,[(n(!0),a(R,null,$(C.subList,(l,g)=>(n(),a("li",{key:l.id,id:"comment_"+l.id},[i("div",Tt,[i("div",Nt,[l.memImg?(n(),a("img",{key:0,src:l.memImg},null,8,At)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",Bt,b(l.viewName),1)]),i("div",Mt,[i("div",{class:"content",innerHTML:l.content},null,8,Et),i("div",Dt,[U(b(l.created_at.substring(0,10))+" ",1),l.modifyButton?(n(),a("div",Rt,[r(d,{class:"whiteButton",onClick:A=>m.deleteSubComment(c,g),variant:"outlined",rounded:""},{default:u(()=>[$t]),_:2},1032,["onClick"]),r(d,{class:"blackButton",onClick:A=>m.setSubModify(c,g),variant:"outlined",rounded:""},{default:u(()=>[Ot]),_:2},1032,["onClick"])])):f("",!0)])])],8,Ut))),128))])):f("",!0)])):f("",!0)],8,at))),128))])]),s.scrolling?(n(),a("div",Vt,[r(w,{size:80,color:"#AD1457",indeterminate:""})])):f("",!0)])}const Pt=T(X,[["render",Ft],["__scopeId","data-v-0df0acad"]]);const xt={components:{articleUpassComponent:G,commentList:Pt},data(){return{util:y,passDialogShow:!1,bid:"",id:"",categoryNames:{},info:{},boardInfo:{},owner:!1,userName:"",memberInfo:{},replyShow:!1,upassType:"modify",commentCnt:0}},created(){this.setClass("sub"),this.setTitle("게시글 상세보기"),this.id=this.$route.params.id,this.bid=this.$route.params.bid;const t=V.getSession();t&&(t!=null&&t.memberInfo)&&(this.memberInfo=t.memberInfo,this.isLogin=!0),this.getData()},methods:{...x(W,["setClass","setTitle"]),getData(){const t={id:this.id,bid:this.bid,type:"view"};L.get(J,t,e=>{if(this.boardInfo=e.data.boardInfo,this.boardInfo.categoryUse=="yes")for(const o of JSON.parse(this.boardInfo.categoryList))this.categoryNames[o.code]=o.name;this.info=e.data.articleInfo,e.data.memberset&&e.data.memberset.nickUse=="yes"&&this.info.nick?this.userName=this.info.nick:this.info.name?this.userName=this.info.name:this.userName=this.info.user_name,this.boardInfo.replyUse=="yes"&&(this.replyShow=!0),this.info.hit=this.info.hit?y.numberFormat(this.info.hit):0,this.info.created_at=this.info.created_at.substring(0,16).replace("T"," "),this.info.secret=="yes"?this.boardInfo.wauth=="all"?this.owner=!0:this.info.user_id&&this.info.user_id==this.memberInfo.id&&(this.owner=!0):this.info.user_id&&this.info.user_id==this.memberInfo.id?this.owner=!0:!this.info.user_id&&this.boardInfo.wauth=="all"&&(this.owner=!0)})},modifyItem(){this.info.secret!="yes"&&!this.info.user_id&&this.boardInfo.wauth=="all"?(this.upassType="modify",this.passDialogShow=!0):this.$router.push("/board/articleRegist/"+this.bid+"/"+this.id)},deleteItem(){if(this.info.secret!="yes"&&!this.info.user_id&&this.boardInfo.wauth=="all")this.upassType="delete",this.passDialogShow=!0;else{const t={type:"confirm",message:"게시글을 삭제 하시겠습니까?",doAction:()=>{this.deleteAction()}};this.emitter.emit("modalOpen",t)}},delAction(t){this.deleteAction()},deleteAction(){const t={id:this.id,bid:this.bid};L.post(z,t,e=>{const o={message:"게시글이 삭제 되었습니다",doAction:()=>{this.$router.push("/board/articleList/"+this.bid)}};this.emitter.emit("modalOpen",o)})},updateCommentCnt(t){this.commentCnt=y.numberFormat(t.total)}},updated(){D(".content img").css("max-width","100%"),D(".content p").css({"line-height":"130%",margin:0})}},H=t=>(F("data-v-47c9c807"),t=t(),P(),t),Ht={class:"page-container"},jt={class:"article-container"},Jt={class:"article-info"},zt={class:"bsubject"},Gt={key:0,class:"category"},Qt={key:1},qt={class:"summary"},Kt={key:0},Wt={key:0,class:"img"},Xt=["src"],Yt=["innerHTML"],Zt={key:1,class:"modify-box"},te=H(()=>i("span",null,"수정",-1)),ee=H(()=>i("span",null,"삭제",-1)),se={class:"button-row"},ie=H(()=>i("span",null,"목록으로",-1));function oe(t,e,o,_,s,m){const h=v("font-awesome-icon"),p=v("v-btn"),d=v("commentList"),k=v("articleUpassComponent");return n(),a("div",Ht,[i("div",jt,[i("ul",Jt,[i("li",zt,[s.info.category?(n(),a("span",Gt,"["+b(s.categoryNames[s.info.category])+"]",1)):f("",!0),U(" "+b(s.info.subject)+" ",1),s.info.secret=="yes"?(n(),a("span",Qt,[r(h,{icon:"fa-solid fa-lock"})])):f("",!0)]),i("li",qt,[i("div",null,[i("span",null,"작성자 : "+b(s.userName),1),s.replyShow?(n(),a("span",Kt,"댓글수 : "+b(s.commentCnt),1)):f("",!0),i("span",null,"조회수 : "+b(s.info.hit),1),i("div",null,"등록일 : "+b(s.info.created_at),1)])])]),s.info.img?(n(),a("div",Wt,[i("img",{src:s.info.img},null,8,Xt)])):f("",!0),i("div",{innerHTML:s.info.content,class:"content"},null,8,Yt),s.owner?(n(),a("div",Zt,[r(p,{class:"white-button",onClick:e[0]||(e[0]=w=>m.modifyItem()),variant:"outlined"},{default:u(()=>[te]),_:1}),r(p,{class:"red-button",onClick:e[1]||(e[1]=w=>m.deleteItem()),variant:"outlined"},{default:u(()=>[ee]),_:1})])):f("",!0),i("div",se,[r(p,{onClick:e[2]||(e[2]=w=>t.$router.back()),class:"whiteButton",variant:"outlined",rounded:"",type:"button"},{default:u(()=>[ie]),_:1})]),s.replyShow?(n(),I(d,{key:2,auth:s.boardInfo.rauth,ptype:"board",onUpdateCommentCnt:m.updateCommentCnt,pid:s.id},null,8,["auth","onUpdateCommentCnt","pid"])):f("",!0)]),r(k,{bid:s.bid,articleId:s.id,type:s.upassType,modelOpen:s.passDialogShow,onUpassSuccess:m.delAction,onParentOpenFalse:e[3]||(e[3]=w=>s.passDialogShow=!1)},null,8,["bid","articleId","type","modelOpen","onUpassSuccess"])])}const ne=T(xt,[["render",oe],["__scopeId","data-v-47c9c807"]]);const me={name:"pcCommentList",props:["auth","ptype","pid"],computed:{...Q(O,["listScrollMore"])},watch:{listScrollMore(t){t&&this.handleLoadMore()}},data(){return{mainForm:null,limit:10,page:1,lastPage:1,total:0,memberImg:"",isLogin:!1,commentList:[],comment:"",commentId:"",memberId:"",insertFlag:!1,formHasErrors:!1,subComment:[],subCommentError:[],subCommentUpdate:[],subCommentId:"",commentIndex:"",scrolling:!1}},created(){const t=V.getSession();t&&(t!=null&&t.memberInfo)&&(this.memberId=t.memberInfo.id,this.isLogin=!0),this.getData()},methods:{...x(O,["setListScroll","setListScrollMore"]),getData(){this.scrolling=!0;const t=this.getSearchParams();L.get(B,t,e=>{var o;this.commentList=[],this.total=y.numberFormat(e.data.commentList.total),this.$emit("updateCommentCnt",{total:e.data.commentList.total}),this.setCommentList(e.data.commentList),this.isLogin&&(this.memberImg=(o=e.data.memberInfo)!=null&&o.img?e.data.memberInfo.img:"")})},getDataList(){this.scrolling=!0;const t=this.getSearchParams();L.get(B,t,e=>{this.setCommentList(e.data.commentList)})},setCommentList(t){this.lastPage=t.last_page;for(let e of t.data){e.viewName=e.memNick?e.memNick:e.memName,e.modifyButton=!!(this.isLogin&&this.memberId==e.user_id);for(let o of e.subList)o.viewName=o.memNick?o.memNick:o.memName,o.modifyButton=!!(this.isLogin&&this.memberId==o.user_id);e.repleShow=!1,this.commentList.push(e)}for(const e in this.commentList)this.subComment[e]="",this.subCommentError[e]="",this.subCommentUpdate[e]=!1;this.scrolling=!1,this.page<this.lastPage?(this.setListScroll(!0),this.setListScrollMore(!1)):this.setListScroll(!1)},getSearchParams(){return{parentType:this.ptype,parentId:this.pid,limit:this.limit,page:this.page}},showRepleBox(t){if(!this.subCommentId)for(let e in this.commentList)t==e?this.commentList[e].repleShow?this.commentList[e].repleShow=!1:this.commentList[e].repleShow=!0:this.commentList[e].repleShow=!1},setModify(t){if(this.subCommentId){const o={message:"기존 수정 작업을 먼저 완료하세요"};this.emitter.emit("modalOpen",o);return}const e=this.commentList[t];this.comment=y.stripTags(e.content),this.commentId=e.id,y.errorPosition("base","#comment-base",()=>{})},setSubModify(t,e){if(this.commentId){const _={message:"기존 수정 작업을 먼저 완료하세요"};this.emitter.emit("modalOpen",_);return}this.commentList[t].repleShow=!0;const o=this.commentList[t].subList[e];this.subComment[t]=y.stripTags(o.content),this.subCommentId=o.id,this.commentIndex=t;for(const _ in this.subCommentUpdate)this.subCommentUpdate[_]=!1;this.subCommentUpdate[t]=!0,y.errorPosition("base","#comment_"+this.commentList[t].id,()=>{})},deleteComment(t){const e={type:"confirm",message:"댓글을 삭제하시겠습니까?",doAction:()=>{const o=this.commentList[t].id;this.blindAction(o,"main")}};this.emitter.emit("modalOpen",e)},deleteSubComment(t,e){const o={type:"confirm",message:"답글을 삭제하시겠습니까?",doAction:()=>{const _=this.commentList[t].subList[e].id;this.blindAction(_,"sub")}};this.emitter.emit("modalOpen",o)},blindAction(t,e){const o={id:t};this.emitter.emit("overlay","open"),L.post(j,o,_=>{this.emitter.emit("overlay","hide"),this.page=1,this.getData();const m={message:(e=="sub"?"답글":"댓글")+"이 삭제되었습니다"};this.emitter.emit("modalOpen",m)})},reset(){this.commentId="",this.comment="",this.$refs.comment.reset()},subReset(t){this.subCommentId="";for(const e in this.subCommentUpdate)this.subCommentUpdate[e]=!1;this.subComment[t]="",this.subCommentError[t]=""},subCommentErrorCheck(t){this.subComment[t]?this.subCommentError[t]="":this.subCommentError[t]="답글을 입력하세요"},updateSubReview(t){if(this.insertFlag)return;if(this.auth=="user"&&!this.isLogin){let s={message:"먼저 로그인 하세요"};this.emitter.emit("modalOpen",s);return}if(!this.subComment[t]){this.subCommentError[t]="답글을 입력하세요";return}this.insertFlag=!0,this.emitter.emit("overlay","open");const e=this.commentList[t].id;let o="",_={parentId:this.pid,parentType:this.ptype,content:this.subComment[t],pid:e};this.subCommentId?(o=M,_.id=this.subCommentId):o=E,L.post(o,_,s=>{if(this.emitter.emit("overlay","hide"),this.insertFlag=!1,this.subCommentId){let p=0,d={};for(let k of this.commentList[this.commentIndex].subList){if(k.id==this.subCommentId){k.content=y.nl2brContent(this.subComment[t]),d=k;break}p++}this.commentList[this.commentIndex].subList[p]=d,y.errorPosition("base","#comment_"+this.subCommentId,()=>{})}else this.page=1,this.getData();const h={message:"답글이 "+(this.subCommentId?"수정":"저장")+" 되었습니다"};this.subReset(this.commentIndex),this.commentIndex="",this.emitter.emit("modalOpen",h)})},updateReview(){if(this.insertFlag)return;if(this.auth=="user"&&!this.isLogin){let o={message:"먼저 로그인 하세요"};this.emitter.emit("modalOpen",o);return}if(!this.mainForm)return;this.insertFlag=!0,this.emitter.emit("overlay","open");let t="",e={parentId:this.pid,parentType:this.ptype,content:this.comment};this.commentId?(t=M,e.id=this.commentId):t=E,L.post(t,e,o=>{if(this.emitter.emit("overlay","hide"),this.insertFlag=!1,this.commentId){let m=0,h={};for(let p of this.commentList){if(p.id==this.commentId){p.content=y.nl2brContent(this.comment),h=p;break}m++}this.commentList[m]=h,y.errorPosition("base","#comment_"+this.commentId,()=>{})}else this.page=1,this.getData();const s={message:"댓글이 "+(this.commentId?"수정":"저장")+" 되었습니다"};this.commentId="",this.comment="",this.$refs.comment.reset(),this.emitter.emit("modalOpen",s)})},handleLoadMore(){this.page<this.lastPage&&(this.page++,this.getDataList())}},destroyed(){this.setListScroll(!1)}},S=t=>(F("data-v-401366d5"),t=t(),P(),t),ae={class:"comment-container",id:"comment-base"},re={class:"comment-title"},ce={class:"form-box"},le={class:"img-box"},de=["src"],he={class:"input-box",id:"c"},ue={key:0,class:"button-box"},_e=S(()=>i("span",null,"댓글 수정",-1)),pe=S(()=>i("span",null,"취소",-1)),fe=S(()=>i("span",null,"댓글 저장",-1)),be={class:"comment-box"},ge={class:"main-comment-ul"},ve=["id"],ye={class:"main-comment"},Ce={class:"user-box"},Ie={class:"img-box"},ke=["src"],Le={class:"uname"},we={class:"content"},Se=["innerHTML"],Ue=["onClick"],Te={class:"rdate"},Ne={key:0,class:"modify-button-box"},Ae=S(()=>i("span",null,"삭제",-1)),Be=S(()=>i("span",null,"수정",-1)),Me={key:0,class:"sub-comment"},Ee={key:0,class:"form-box"},De={class:"img-box"},Re=["src"],$e={class:"input-box"},Oe={key:0,class:"button-box"},Ve=S(()=>i("span",null,"답글 수정",-1)),Fe=S(()=>i("span",null,"취소",-1)),Pe=S(()=>i("span",null,"답글 저장",-1)),xe={class:"sub-comment-ul"},He=["id"],je={class:"user-box"},Je={class:"img-box"},ze=["src"],Ge={class:"uname"},Qe=["innerHTML"],qe={class:"rdate"},Ke={key:0,class:"modify-button-box"},We=S(()=>i("span",null,"삭제",-1)),Xe=S(()=>i("span",null,"수정",-1)),Ye={key:0,style:{"text-align":"center",padding:"200px 0"}};function Ze(t,e,o,_,s,m){const h=v("font-awesome-icon"),p=v("v-textarea"),d=v("v-btn"),k=v("v-form"),w=v("v-text-field"),C=v("v-progress-circular");return n(),a("div",ae,[i("div",re,"댓글 ("+b(s.total)+")",1),r(k,{modelValue:s.mainForm,"onUpdate:modelValue":e[2]||(e[2]=c=>s.mainForm=c),onSubmit:q(m.updateReview,["prevent"])},{default:u(()=>[i("div",ce,[i("div",le,[s.memberImg?(n(),a("img",{key:0,src:s.memberImg},null,8,de)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",he,[r(p,{ref:"comment",modelValue:s.comment,"onUpdate:modelValue":e[0]||(e[0]=c=>s.comment=c),variant:"outlined",density:"compact",rows:"2",maxLength:"250","no-resize":"",rules:[()=>!!s.comment||"댓글을 입력하세요"]},null,8,["modelValue","rules"])]),s.commentId?(n(),a("div",ue,[r(d,{class:"blackButton",type:"submit",variant:"outlined"},{default:u(()=>[_e]),_:1}),r(d,{class:"whiteButton",onClick:e[1]||(e[1]=c=>m.reset()),variant:"outlined"},{default:u(()=>[pe]),_:1})])):(n(),I(d,{key:1,class:"blackButton",type:"submit",variant:"outlined"},{default:u(()=>[fe]),_:1}))])]),_:1},8,["modelValue","onSubmit"]),i("div",be,[i("ul",ge,[(n(!0),a(R,null,$(s.commentList,(c,l)=>(n(),a("li",{key:c.id,id:"comment_"+c.id},[i("div",ye,[i("div",Ce,[i("div",Ie,[c.memImg?(n(),a("img",{key:0,src:c.memImg},null,8,ke)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",Le,b(c.viewName),1)]),i("div",we,[i("div",{innerHTML:c.content},null,8,Se),i("div",{class:"reply-action-text",onClick:g=>m.showRepleBox(l)}," 답글달기 ",8,Ue)]),i("div",Te,[U(b(c.created_at.substring(0,10))+" ",1),c.modifyButton?(n(),a("div",Ne,[r(d,{class:"whiteButton",onClick:g=>m.deleteComment(l),variant:"outlined",rounded:""},{default:u(()=>[Ae]),_:2},1032,["onClick"]),r(d,{class:"blackButton",onClick:g=>m.setModify(l),variant:"outlined",rounded:""},{default:u(()=>[Be]),_:2},1032,["onClick"])])):f("",!0)])]),c.subList.length>0||c.repleShow?(n(),a("div",Me,[c.repleShow?(n(),a("div",Ee,[i("div",De,[s.memberImg?(n(),a("img",{key:0,src:s.memberImg},null,8,Re)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",$e,[r(w,{"error-messages":s.subCommentError[l],variant:"outlined","bg-color":"#fff",modelValue:s.subComment[l],"onUpdate:modelValue":g=>s.subComment[l]=g,density:"compact",maxLength:"150",onInput:g=>m.subCommentErrorCheck(l),onBlur:g=>m.subCommentErrorCheck(l)},null,8,["error-messages","modelValue","onUpdate:modelValue","onInput","onBlur"])]),s.subCommentUpdate[l]?(n(),a("div",Oe,[r(d,{class:"blackButton",onClick:g=>m.updateSubReview(l),variant:"outlined"},{default:u(()=>[Ve]),_:2},1032,["onClick"]),r(d,{class:"whiteButton",onClick:g=>m.subReset(l),variant:"outlined"},{default:u(()=>[Fe]),_:2},1032,["onClick"])])):(n(),I(d,{key:1,class:"blackButton",onClick:g=>m.updateSubReview(l),variant:"outlined"},{default:u(()=>[Pe]),_:2},1032,["onClick"]))])):f("",!0),i("ul",xe,[(n(!0),a(R,null,$(c.subList,(g,A)=>(n(),a("li",{key:g.id,id:"comment_"+g.id},[i("div",je,[i("div",Je,[g.memImg?(n(),a("img",{key:0,src:g.memImg},null,8,ze)):(n(),I(h,{key:1,class:"icon",icon:"fa-solid fa-user"}))]),i("div",Ge,b(g.viewName),1)]),i("div",{class:"content",innerHTML:g.content},null,8,Qe),i("div",qe,[U(b(g.created_at.substring(0,10))+" ",1),g.modifyButton?(n(),a("div",Ke,[r(d,{class:"whiteButton",onClick:K=>m.deleteSubComment(l,A),variant:"outlined",rounded:""},{default:u(()=>[We]),_:2},1032,["onClick"]),r(d,{class:"blackButton",onClick:K=>m.setSubModify(l,A),variant:"outlined",rounded:""},{default:u(()=>[Xe]),_:2},1032,["onClick"])])):f("",!0)])],8,He))),128))])])):f("",!0)],8,ve))),128))])]),s.scrolling?(n(),a("div",Ye,[r(C,{size:80,color:"#AD1457",indeterminate:""})])):f("",!0)])}const ts=T(me,[["render",Ze],["__scopeId","data-v-401366d5"]]);const es={components:{articleUpassComponent:G,commentList:ts},data(){return{passDialogShow:!1,bid:"",id:"",util:y,categoryNames:{},info:{},boardInfo:{},owner:!1,userName:"",memberInfo:{},replyShow:!1,upassType:"modify",commentCnt:0}},created(){this.id=this.$route.params.id,this.bid=this.$route.params.bid;const t=V.getSession();t&&(t!=null&&t.memberInfo)&&(this.memberInfo=t.memberInfo,this.isLogin=!0),this.getData()},methods:{getData(){const t={id:this.id,bid:this.bid,type:"view"};L.get(J,t,e=>{if(this.boardInfo=e.data.boardInfo,this.boardInfo.categoryUse=="yes")for(const o of JSON.parse(this.boardInfo.categoryList))this.categoryNames[o.code]=o.name;this.boardInfo.replyUse=="yes"&&(this.replyShow=!0),this.info=e.data.articleInfo,e.data.memberset&&e.data.memberset.nickUse=="yes"&&this.info.nick?this.userName=this.info.nick:this.info.name?this.userName=this.info.name:this.userName=this.info.user_name,this.info.hit=this.info.hit?y.numberFormat(this.info.hit):0,this.info.created_at=this.info.created_at.substring(0,16).replace("T"," "),this.info.secret=="yes"?this.boardInfo.wauth=="all"?this.owner=!0:this.info.user_id&&this.info.user_id==this.memberInfo.id&&(this.owner=!0):this.info.user_id&&this.info.user_id==this.memberInfo.id?this.owner=!0:!this.info.user_id&&this.boardInfo.wauth=="all"&&(this.owner=!0)})},modifyItem(){this.info.secret!="yes"&&!this.info.user_id&&this.boardInfo.wauth=="all"?(this.upassType="modify",this.passDialogShow=!0):this.$router.push("/board/articleRegist/"+this.bid+"/"+this.id)},deleteItem(){if(this.info.secret!="yes"&&!this.info.user_id&&this.boardInfo.wauth=="all")this.upassType="delete",this.passDialogShow=!0;else{const t={type:"confirm",message:"게시글을 삭제 하시겠습니까?",doAction:()=>{this.deleteAction()}};this.emitter.emit("modalOpen",t)}},delAction(t){this.deleteAction()},updateCommentCnt(t){this.commentCnt=y.numberFormat(t.total)},deleteAction(){const t={id:this.id,bid:this.bid};L.post(z,t,e=>{const o={message:"게시글이 삭제 되었습니다",doAction:()=>{this.$router.push("/board/articleList/"+this.bid)}};this.emitter.emit("modalOpen",o)})}},updated(){D(".content img").css("max-width","100%"),D(".content p").css({"line-height":"130%",margin:0})}},ss=t=>(F("data-v-631860e7"),t=t(),P(),t),is={class:"page-container"},os={class:"article-container"},ns={class:"article-info"},ms={class:"bname"},as={class:"bsubject"},rs={key:0,class:"category"},cs={key:1},ls={class:"summary"},ds={key:0},hs={key:0},us={key:0,class:"img"},_s=["src"],ps=["innerHTML"],fs={class:"button-row"},bs=ss(()=>i("span",null,"목록으로",-1));function gs(t,e,o,_,s,m){const h=v("font-awesome-icon"),p=v("v-btn"),d=v("commentList"),k=v("articleUpassComponent");return n(),a("div",is,[i("div",os,[i("ul",ns,[i("li",ms,b(s.boardInfo.bname),1),i("li",as,[s.info.category?(n(),a("span",rs,"["+b(s.categoryNames[s.info.category])+"]",1)):f("",!0),U(" "+b(s.info.subject)+" ",1),s.info.secret=="yes"?(n(),a("span",cs,[r(h,{icon:"fa-solid fa-lock"})])):f("",!0)]),i("li",ls,[i("div",null,[i("span",null,"작성자 : "+b(s.userName),1),s.replyShow?(n(),a("span",ds,"댓글수 : "+b(s.commentCnt),1)):f("",!0),i("span",null,"조회수 : "+b(s.info.hit),1),i("span",null,"등록일 : "+b(s.info.created_at),1)]),s.owner?(n(),a("div",hs,[i("span",{class:"underline",onClick:e[0]||(e[0]=w=>m.modifyItem())},"수정"),i("span",{class:"underline",onClick:e[1]||(e[1]=w=>m.deleteItem())},"삭제")])):f("",!0)])]),s.info.img?(n(),a("div",us,[i("img",{src:s.info.img},null,8,_s)])):f("",!0),i("div",{innerHTML:s.info.content,class:"content"},null,8,ps),i("div",fs,[r(p,{onClick:e[2]||(e[2]=w=>t.$router.back()),class:"whiteButton",variant:"outlined",rounded:"",type:"button"},{default:u(()=>[bs]),_:1})]),s.replyShow?(n(),I(d,{key:1,auth:s.boardInfo.rauth,ptype:"board",onUpdateCommentCnt:m.updateCommentCnt,pid:s.id},null,8,["auth","onUpdateCommentCnt","pid"])):f("",!0)]),r(k,{bid:s.bid,articleId:s.id,type:s.upassType,modelOpen:s.passDialogShow,onUpassSuccess:m.delAction,onParentOpenFalse:e[3]||(e[3]=w=>s.passDialogShow=!1)},null,8,["bid","articleId","type","modelOpen","onUpassSuccess"])])}const vs=T(es,[["render",gs],["__scopeId","data-v-631860e7"]]);const ys={components:{mobileArticleView:ne,pcArticleView:vs},data(){return{mobile:!1}},created(){this.mobile=this.$isMobile()}},Cs={class:"page-container"};function Is(t,e,o,_,s,m){const h=v("mobileArticleView"),p=v("pcArticleView");return n(),a("div",Cs,[s.mobile?(n(),I(h,{key:0})):(n(),I(p,{key:1}))])}const Bs=T(ys,[["render",Is],["__scopeId","data-v-b9c3b3b4"]]);export{Bs as default};
