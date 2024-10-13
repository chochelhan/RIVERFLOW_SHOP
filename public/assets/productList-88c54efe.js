import{e as T,a as k,S as R,T as M,U as F}from"./apiService-6a3981ff.js";import{U}from"./util-c02dbcf9.js";import{i as E,m as N,_ as B,r as b,o as r,c as d,a as o,b as I,w,F as f,g as _,j as y,n as C,t as u,k as L,e as S,p as $,f as A}from"./index-7c0b344a.js";import{m as O}from"./mobile-8dd88b05.js";import{c as D}from"./config-9369631b.js";import{S as W,a as G}from"./swiper-66c2bd3a.js";import{J as P}from"./jquery-d0ecd42c.js";import{_ as V}from"./empty_heart-d5f42d80.js";import{_ as z}from"./sm_heart-055da75c.js";import{p as H}from"./paging-03a5ea24.js";import"./axios-c24e582b.js";const J={name:"mobileProductList",components:{Swiper:W,SwiperSlide:G},watch:{getListScrollMore(s){s&&this.handleLoadMore()}},computed:{...E(D,["listScrollMore"])},data(){return{util:U,info:{},productList:[],categorys:[],subCategorys:[],brands:[],priceRange:[1e4,7e5],priceMin:1e3,priceMax:1e6,categoryListNames:{},memberInfo:{},page:1,limit:12,total:0,lastPage:"",orderBy:"created_at",orderName:"최신순",category:"",firstCategory:"",firstCategoryDepth:"",brandId:"",orderByShow:!1,categoryBase:"",categoryTitle:"Category",sortList:[{code:"created_at",name:"최신순",active:"active"},{code:"favorite",name:"인기순",active:""},{code:"lowprice",name:"가격낮은순",active:""},{code:"highprice",name:"가격높은순",active:""}],scrolling:!1}},created(){this.emitter.on("productListReload",this.productListReload),localStorage.setItem("productList","create");const s=this.$route.params.category;this.getInit(s)},activated(){const s=T.getSession();s&&s.memberInfo&&(this.memberInfo=s.memberInfo),this.setClass("main"),this.setTitle(""),sessionStorage.getItem("eventSlideTop")&&setTimeout(()=>{P("html,body").scrollTop(sessionStorage.getItem("eventSlideTop")),sessionStorage.setItem("eventSlideTop","")},50)},methods:{...N(O,["setClass","setTitle"]),...N(D,["setListScroll","setListScrollMore"]),orderByToggle(){this.orderByShow?this.orderByShow=!1:this.orderByShow=!0},setOrderBy(s){for(let t of this.sortList)s==t.code?(t.active="active",this.orderBy=t.code,this.orderName=t.name):t.active="";this.page=1,this.productList=[],this.getDataList(),this.orderByShow=!1},getInit(s){if(s!="default"){const t=s.split("_");switch(t.length){case 1:this.firstCategory=t[0],this.category=t[0],this.firstCategoryDepth=1;break;case 2:this.firstCategory=t[1],this.category=t[1],this.firstCategoryDepth=1;break;case 3:this.firstCategory=t[2],this.category=t[2],this.firstCategoryDepth=2;break}}else this.categoryBase="default";this.getData()},getData(){this.scrolling=!0;const s=this.getSearchParams();k.get(R,s,t=>{var c,i,e;this.productList=[],this.categorys=[],this.brands=[],this.categoryTitle="Category",this.categoryBase=="default"&&(this.categorys=[{id:"all",gclass:"active",name:"전체",subList:""}]);for(const a of t.data.categoryList){let g=[];if(this.categoryListNames[a.id]=a.cname,((c=a.subList)==null?void 0:c.length)>0){if(this.categoryBase=="default"){let h={id:"all",name:a.cname+" 전체",gclass:"active"};g.push(h)}for(const h of a.subList){if(this.categoryListNames[h.id]=h.cname,this.categoryBase=="default"){let m={id:h.id,name:h.cname,gclass:"",depth:h.depth};g.push(m)}if(((i=h.subList)==null?void 0:i.length)>0)for(const m of h.subList)this.categoryListNames[m.id]=m.cname}}if(this.categoryBase=="default"){let h={id:a.id,name:a.cname,gclass:"",depth:a.depth,subList:g};this.categorys.push(h)}}if(this.categoryBase!="default"){this.categoryTitle=t.data.categoryInfo.cname,this.categorys=[{id:t.data.categoryInfo.id,gclass:"active",name:"전체",subList:""}];for(const a of t.data.categoryDataList){let g=[];if(((e=a.subList)==null?void 0:e.length)>0){let m={id:"all",name:a.cname+" 전체",gclass:"active"};g.push(m);for(const l of a.subList){let n={id:l.id,name:l.cname,gclass:"",depth:l.depth};g.push(n)}}let h={id:a.id,name:a.cname,gclass:"",depth:a.depth,subList:g};this.categorys.push(h)}}this.brands=[{id:"all",gclass:"active",name:"전체"}];for(const a of t.data.brandList){let g={id:a.id,name:a.bname,gclass:""};this.brands.push(g)}this.setDataList(t.data.productList)})},getDataList(){this.scrolling=!0;const s=this.getSearchParams();k.get(M,s,t=>{this.setDataList(t.data)})},setDataList(s){this.total=s.total,this.lastPage=s.last_page;for(let t of s.data){let c="";if(this.category)c=this.getCategoryNameFind(t);else{let e=t.category1.split(",");c=this.categoryListNames[e[0]],e[1]&&(c+=" > "+this.categoryListNames[e[1]]),e[2]&&(c+=" > "+this.categoryListNames[e[2]])}let i;this.memberInfo.id?i=!!t.myWish:i=!1,t.wishIcon=i,t.category=c,this.productList.push(t)}this.scrolling=!1,this.page<this.lastPage?(this.setListScroll(!0),this.setListScrollMore(!1)):this.setListScroll(!1)},getCategoryNameFind(s){let t="";for(let c=1;c<=3&&!t;c++)if(s["category"+c]){let i=s["category"+c].split(",");switch(this.firstCategoryDepth){case 1:this.firstCategory==i[0]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break;case 2:this.firstCategory==i[1]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break;case 3:this.firstCategory==i[2]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break}}return t},goCategory(s){for(let t of this.categorys)t.gclass="";if(this.categorys[s].gclass="active",this.subCategorys=[],this.categorys[s].subList.length>0){for(let t of this.categorys[s].subList)t.gclass="",this.subCategorys.push(t);this.categorys[s].subList[0].gclass="active"}this.categorys[s].id=="all"?(this.category="",this.firstCategory=""):(this.category=this.categorys[s].id,this.firstCategory=this.categorys[s].id,this.firstCategoryDepth=this.categorys[s].depth),this.page=1,this.productList=[],this.getDataList()},goSubCategory(s){for(let c of this.subCategorys)c.gclass="";let t=this.subCategorys[s];t.gclass="active",t.id=="all"?this.category=this.firstCategory:this.category=t.id,this.page=1,this.productList=[],this.getDataList()},goBrand(s){for(let t of this.brands)t.gclass="";this.brands[s].gclass="active",this.brands[s].id=="all"?this.brandId="":this.brandId=this.brands[s].id,this.getDataList()},priceSearch(s){this.getDataList()},getSearchParams(){return{category:this.category,orderByField:this.orderBy,page:this.page,limit:this.limit}},productDetail(s){const t=this.category?this.category:"default";sessionStorage.setItem("eventSlideTop",P("html,body").scrollTop()),this.$router.push("/product/productView/"+s+"/"+t)},productWish(s){const t=this.productList[s].id;if(!this.memberInfo.id){let i={type:"confirm",message:"회원만 사용 가능합니다<br>로그인 하시겠습니까",doAction:()=>{this.$router.push("/memberLogin")}};this.emitter.emit("modalOpen",i);return}const c={pid:t};k.post(F,c,i=>{let e=this.productList[s];e.wish=i.data.total,i.data.rtype=="delete"?e.wishIcon=!1:e.wishIcon=!0,this.productList[s]=e})},productListReload(s){this.info={},this.priceRange=[1e4,7e5],this.priceMin=1e3,this.priceMax=1e6,this.categoryListNames={},this.memberInfo={},this.page=1,this.lastPage="",this.orderBy="created_at",this.orderName="최신순",this.category="",this.firstCategory="",this.scrolling=!1,this.firstCategoryDepth="",this.brandId="",this.orderByShow=!1,this.categoryBase="",this.subCategorys=[],this.productList=[],this.getInit(s.category)},handleLoadMore(){this.page<this.lastPage&&(this.page++,this.getDataList())}},beforeUnmount(){this.emitter.off("productListReload"),localStorage.setItem("productList","")}},j=s=>($("data-v-733f3e7c"),s=s(),A(),s),Q={class:"page-container"},q={class:"menu-container"},K=["onClick"],X={key:0,class:"sub-menu-container"},Y=["onClick"],Z={class:"list-container"},tt={class:"sort-box"},st={class:"total"},et={class:"orderby-container"},it={class:"icon"},at={class:"label"},ot={key:0,class:"orderby-list elevation-3"},rt=["onClick"],ct={class:"product-list"},lt={class:"img"},nt=["onClick","src"],ht=["onClick"],dt={key:1,src:V},gt=["onClick"],ut={class:"category"},mt={class:"like-box"},pt=j(()=>o("img",{src:z},null,-1)),yt={class:"pname string-cut"},ft={class:"price"},_t={key:0},bt={style:{"text-decoration":"line-through","padding-right":"10px"}},Lt={key:1},vt={key:0,style:{"text-align":"center",padding:"200px 0"}};function Ct(s,t,c,i,e,a){const g=b("swiper-slide"),h=b("swiper"),m=b("font-awesome-icon"),l=b("v-progress-circular");return r(),d("div",Q,[o("div",q,[I(h,{slidesPerView:"auto",ref:"menuSwiper"},{default:w(()=>[(r(!0),d(f,null,_(e.categorys,(n,p)=>(r(),y(g,{style:{width:"auto"},class:C(n.gclass),key:"cat"+n.id},{default:w(()=>[o("div",{class:C("cate-item "+n.gclass),onClick:v=>a.goCategory(p)},u(n.name),11,K)]),_:2},1032,["class"]))),128))]),_:1},512)]),e.subCategorys.length>0?(r(),d("div",X,[I(h,{slidesPerView:"auto"},{default:w(()=>[(r(!0),d(f,null,_(e.subCategorys,(n,p)=>(r(),y(g,{style:{width:"auto"},class:C(n.gclass),key:"cat"+n.id},{default:w(()=>[o("div",{class:C("cate-item "+n.gclass),onClick:v=>a.goSubCategory(p)},u(n.name),11,Y)]),_:2},1032,["class"]))),128))]),_:1})])):L("",!0),o("div",Z,[o("div",tt,[o("div",st,[o("span",null,u(e.util.numberFormat(e.total)),1),S("개의 상품 ")]),o("div",et,[o("div",{class:"orderby-box",onClick:t[0]||(t[0]=n=>a.orderByToggle())},[o("div",it,[e.orderByShow?(r(),y(m,{key:1,icon:"fa-solid fa-angle-up"})):(r(),y(m,{key:0,icon:"fa-solid fa-angle-down"}))]),o("div",at,u(e.orderName),1)]),e.orderByShow?(r(),d("ul",ot,[(r(!0),d(f,null,_(e.sortList,(n,p)=>(r(),d("li",{key:"srt"+p,onClick:v=>a.setOrderBy(n.code)},[o("span",null,[n.active?(r(),y(m,{key:0,icon:"fa-solid fa-check"})):L("",!0)]),o("div",null,u(n.name),1)],8,rt))),128))])):L("",!0)])]),o("ul",ct,[(r(!0),d(f,null,_(e.productList,(n,p)=>(r(),d("li",null,[o("div",lt,[o("img",{onClick:v=>a.productDetail(n.id),class:"list-img",src:n.listImg,style:{"max-width":"100%"}},null,8,nt),o("div",{class:"icon-box",onClick:v=>a.productWish(p)},[n.wishIcon?(r(),y(m,{key:0,class:"icon",icon:"fa-solid fa-heart"})):(r(),d("img",dt))],8,ht)]),o("div",{class:"content",onClick:v=>a.productDetail(n.id)},[o("div",ut,[S(u(n.category)+" ",1),o("div",mt,[pt,S(" "+u(e.util.numberFormat(n.wish)),1)])]),o("div",yt,u(n.pname),1),o("div",ft,[n.dcprice>0&&parseInt(n.price)>parseInt(n.dcprice)?(r(),d("span",_t,[o("span",bt,u(e.util.numberFormat(n.price))+"원",1),o("span",null,u(e.util.numberFormat(n.dcprice))+"원",1)])):(r(),d("span",Lt,u(e.util.numberFormat(n.price))+"원 ",1))])],8,gt)]))),256))]),e.scrolling?(r(),d("div",vt,[I(l,{size:80,color:"#AD1457",indeterminate:""})])):L("",!0)])])}const kt=B(J,[["render",Ct],["__scopeId","data-v-733f3e7c"]]);const It={name:"pcProductList",components:{paging:H},data(){return{util:U,info:{},productList:[],categorys:[],brands:[],priceRange:[1e3,1e7],priceMin:1e3,priceMax:1e7,categoryListNames:{},memberInfo:{},page:1,limit:12,pageGroup:10,paging:{},pagingReload:!1,orderBy:"created_at",orderName:"최신순",category:"",firstCategory:"",firstCategoryDepth:"",brandId:"",orderByShow:!1,categoryBase:"",categoryTitle:"Category",sortList:[{code:"created_at",name:"최신순",active:"active"},{code:"favorite",name:"인기순",active:""},{code:"lowprice",name:"가격낮은순",active:""},{code:"highprice",name:"가격높은순",active:""}]}},created(){this.emitter.on("productListReload",this.productListReload);const s=T.getSession();s&&s.memberInfo&&(this.memberInfo=s.memberInfo);const t=this.$route.params.category;this.getInit(t)},methods:{orderByToggle(){this.orderByShow?this.orderByShow=!1:this.orderByShow=!0},setOrderBy(s){for(let t of this.sortList)s==t.code?(t.active="active",this.orderBy=t.code,this.orderName=t.name):t.active="";this.page=1,this.productList=[],this.getDataList(),this.orderByShow=!1},getInit(s){if(s!="default"){const t=s.split("_");switch(t.length){case 1:this.firstCategory=t[0],this.category=t[0],this.firstCategoryDepth=1;break;case 2:this.firstCategory=t[1],this.category=t[1],this.firstCategoryDepth=1;break;case 3:this.firstCategory=t[2],this.category=t[2],this.firstCategoryDepth=2;break}}else this.categoryBase="default";this.getData()},getData(){const s=this.getSearchParams();this.scrolling=!0,console.log(s),k.get(R,s,t=>{var c,i,e;this.productList=[],this.categorys=[],this.brands=[],this.categoryTitle="Category",this.categoryBase=="default"&&(this.categorys=[{id:"all",gclass:"active",name:"전체",subList:""}]);for(const a of t.data.categoryList){let g=[];if(this.categoryListNames[a.id]=a.cname,((c=a.subList)==null?void 0:c.length)>0){if(this.categoryBase=="default"){let h={id:"all",name:a.cname+" 전체",gclass:"active"};g.push(h)}for(const h of a.subList){if(this.categoryListNames[h.id]=h.cname,this.categoryBase=="default"){let m={id:h.id,name:h.cname,gclass:"",depth:h.depth};g.push(m)}if(((i=h.subList)==null?void 0:i.length)>0)for(const m of h.subList)this.categoryListNames[m.id]=m.cname}}if(this.categoryBase=="default"){let h={id:a.id,name:a.cname,gclass:"",depth:a.depth,subList:g};this.categorys.push(h)}}if(this.categoryBase!="default"){this.categoryTitle=t.data.categoryInfo.cname,this.categorys=[{id:t.data.categoryInfo.id,gclass:"active",name:"전체",subList:"",depth:1}];for(const a of t.data.categoryDataList){let g=[];if(((e=a.subList)==null?void 0:e.length)>0){let m={id:"all",name:a.cname+" 전체",gclass:"active"};g.push(m);for(const l of a.subList){let n={id:l.id,name:l.cname,gclass:"",depth:l.depth};g.push(n)}}let h={id:a.id,name:a.cname,gclass:"",depth:a.depth,subList:g};this.categorys.push(h)}}this.brands=[{id:"all",gclass:"active",name:"전체"}];for(const a of t.data.brandList){let g={id:a.id,name:a.bname,gclass:""};this.brands.push(g)}this.setDataList(t.data.productList)})},getDataList(){const s=this.getSearchParams();k.get(M,s,t=>{this.productList=[],this.setDataList(t.data)})},pageMode(s){this.page=s.page,this.getDataList()},setDataList(s){this.paging=s,this.pagingReload=!0,setTimeout(()=>{this.pagingReload=!1},100);for(let t of s.data){let c="";if(this.category)c=this.getCategoryNameFind(t);else{let e=t.category1.split(",");c=this.categoryListNames[e[0]],e[1]&&(c+=" > "+this.categoryListNames[e[1]]),e[2]&&(c+=" > "+this.categoryListNames[e[2]])}let i;this.memberInfo.id?i=!!t.myWish:i=!0,t.wishIcon=i,t.category=c,this.productList.push(t)}},getCategoryNameFind(s){let t="";for(let c=1;c<=3&&!t;c++)if(s["category"+c]){let i=s["category"+c].split(",");switch(this.firstCategoryDepth){case 1:this.firstCategory==i[0]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break;case 2:this.firstCategory==i[1]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break;case 3:this.firstCategory==i[2]&&(t=this.categoryListNames[i[0]],i[1]&&(t+=" > "+this.categoryListNames[i[1]]),i[2]&&(t+=" > "+this.categoryListNames[i[2]]));break}}return t},goCategory(s){for(let t of this.categorys)t.gclass="";if(this.categorys[s].gclass="active",this.categorys[s].subList.length>0){for(let t of this.categorys[s].subList)t.gclass="";this.categorys[s].subList[0].gclass="active"}this.categorys[s].id=="all"?this.category="":(this.category=this.categorys[s].id,this.firstCategory=this.categorys[s].id,this.firstCategoryDepth=this.categorys[s].depth),this.page=1,this.productList=[],this.getDataList()},goSubCategory(s,t){for(let i of this.categorys[s].subList)i.gclass="";let c=this.categorys[s].subList[t];c.gclass="active",this.categorys[s].subList[t]=c,c.id=="all"?this.category=this.categorys[s].id:this.category=c.id,this.page=1,this.productList=[],this.getDataList()},goBrand(s){for(let t of this.brands)t.gclass="";this.brands[s].gclass="active",this.brands[s].id=="all"?this.brandId="":this.brandId=this.brands[s].id,this.page=1,this.productList=[],this.getDataList()},priceSearch(s){this.page=1,this.productList=[],this.getDataList()},getSearchParams(){return{category:this.category,brandId:this.brandId,minPrice:this.priceRange[0],maxPrice:this.priceRange[1],orderByField:this.orderBy,page:this.page,limit:this.limit}},productDetail(s){const t=this.category?this.category:"default";this.$router.push("/product/productView/"+s+"/"+t)},productWish(s){const t=this.productList[s].id;if(!this.memberInfo.id){let i={type:"confirm",message:"회원만 사용 가능합니다<br>로그인 하시겠습니까",doAction:()=>{this.$router.push("/memberLogin")}};this.emitter.emit("modalOpen",i);return}const c={pid:t};k.post(F,c,i=>{let e=this.productList[s];e.wish=i.data.total,i.data.rtype=="delete"?e.wishIcon=!1:e.wishIcon=!0,this.productList[s]=e})},productListReload(s){this.info={},this.priceRange=[1e3,1e7],this.priceMin=1e3,this.priceMax=1e7,this.categoryListNames={},this.memberInfo={},this.page=1,this.lastPage="",this.scrolling=!1,this.orderBy="created_at",this.orderName="최신순",this.category="",this.firstCategory="",this.firstCategoryDepth="",this.brandId="",this.orderByShow=!1,this.categoryBase="",this.getInit(s.category)}},destroyed(){this.emitter.off("productListReload")}},x=s=>($("data-v-6eac7d1e"),s=s(),A(),s),St={class:"page-container"},wt={class:"menu-container"},Bt={class:"sub-title"},Nt={class:"category-ul"},Dt=["onClick"],Pt={key:0,class:"sub-category-ul"},Tt=["onClick"],Rt=x(()=>o("div",{class:"sub-title top-padding"},"Brand",-1)),Mt={class:"category-ul"},Ft=["onClick"],Ut={class:"sub-title top-padding"},$t={class:"price-box"},At={class:"list-container"},Vt={class:"sort-box"},xt=x(()=>o("div",{class:"empty-box"},null,-1)),Et={class:"orderby-container"},Ot={class:"icon"},Wt={class:"label"},Gt={key:0,class:"orderby-list elevation-3"},zt=["onClick"],Ht={class:"product-list"},Jt={class:"img"},jt=["onClick","src"],Qt=["onClick"],qt={key:1,src:V},Kt=["onClick"],Xt={class:"category"},Yt={class:"like-box"},Zt={class:"pname string-cut"},ts={class:"price"},ss={key:0},es={style:{"text-decoration":"line-through","padding-right":"10px"}},is={key:1};function as(s,t,c,i,e,a){const g=b("v-range-slider"),h=b("font-awesome-icon"),m=b("paging");return r(),d("div",St,[o("div",wt,[o("div",Bt,u(e.categoryTitle),1),o("ul",Nt,[(r(!0),d(f,null,_(e.categorys,(l,n)=>(r(),d("li",{class:C(l.gclass),key:"cat"+l.id},[o("div",{class:"cate-item",onClick:p=>a.goCategory(n)},u(l.name),9,Dt),l.subList.length>0&&l.gclass=="active"?(r(),d("ul",Pt,[(r(!0),d(f,null,_(l.subList,(p,v)=>(r(),d("li",{class:C(p.gclass),key:"cat"+p.id},[o("div",{class:"sub-cate-item",onClick:hs=>a.goSubCategory(n,v)},u(p.name),9,Tt)],2))),128))])):L("",!0)],2))),128))]),Rt,o("ul",Mt,[(r(!0),d(f,null,_(e.brands,(l,n)=>(r(),d("li",{class:C(l.gclass),key:"brd"+l.id},[o("div",{onClick:p=>a.goBrand(n),class:"cate-item"},u(l.name),9,Ft)],2))),128))]),o("div",Ut,[S("Price "),o("span",null,u(e.util.numberFormat(e.priceRange[0]))+"원 - "+u(e.util.numberFormat(e.priceRange[1]))+"원",1)]),o("div",$t,[I(g,{modelValue:e.priceRange,"onUpdate:modelValue":t[0]||(t[0]=l=>e.priceRange=l),max:e.priceMax,min:e.priceMin,color:"#000","track-color":"#E0E2E6",class:"align-center",onEnd:a.priceSearch},null,8,["modelValue","max","min","onEnd"])])]),o("div",At,[o("div",Vt,[xt,o("div",Et,[o("div",{class:"orderby-box",onClick:t[1]||(t[1]=l=>a.orderByToggle())},[o("div",Ot,[e.orderByShow?(r(),y(h,{key:1,icon:"fa-solid fa-angle-up"})):(r(),y(h,{key:0,icon:"fa-solid fa-angle-down"}))]),o("div",Wt,u(e.orderName),1)]),e.orderByShow?(r(),d("ul",Gt,[(r(!0),d(f,null,_(e.sortList,(l,n)=>(r(),d("li",{key:"srt"+n,onClick:p=>a.setOrderBy(l.code)},[o("span",null,[l.active?(r(),y(h,{key:0,icon:"fa-solid fa-check"})):L("",!0)]),o("div",null,u(l.name),1)],8,zt))),128))])):L("",!0)])]),o("ul",Ht,[(r(!0),d(f,null,_(e.productList,(l,n)=>(r(),d("li",null,[o("div",Jt,[o("img",{onClick:p=>a.productDetail(l.id),class:"list-img",src:l.listImg,style:{"max-width":"100%"}},null,8,jt),o("div",{class:"icon-box",onClick:p=>a.productWish(n)},[l.wishIcon?(r(),y(h,{key:0,class:"icon",icon:"fa-solid fa-heart"})):(r(),d("img",qt))],8,Qt)]),o("div",{class:"content",onClick:p=>a.productDetail(l.id)},[o("div",Xt,[S(u(l.category)+" ",1),o("div",Yt,[I(h,{class:"icon",icon:"fa-solid fa-heart"}),S(" "+u(e.util.numberFormat(l.wish)),1)])]),o("div",Zt,u(l.pname),1),o("div",ts,[l.dcprice>0&&parseInt(l.price)>parseInt(l.dcprice)?(r(),d("span",ss,[o("span",es,u(e.util.numberFormat(l.price))+"원",1),o("span",null,u(e.util.numberFormat(l.dcprice))+"원",1)])):(r(),d("span",is,u(e.util.numberFormat(l.price))+"원 ",1))])],8,Kt)]))),256))]),I(m,{paging:e.paging,pageGroup:e.pageGroup,reload:e.pagingReload,onPageMove:a.pageMode},null,8,["paging","pageGroup","reload","onPageMove"])])])}const os=B(It,[["render",as],["__scopeId","data-v-6eac7d1e"]]);const rs={name:"productList",components:{mobileProductList:kt,pcProductList:os},data(){return{mobile:!1,pageLoad:!1}},created(){this.mobile=this.$isMobile(),this.pageLoad=!0}},cs={class:"page-container"},ls={key:0};function ns(s,t,c,i,e,a){const g=b("mobileProductList"),h=b("pcProductList");return r(),d("div",cs,[e.pageLoad?(r(),d("div",ls,[e.mobile?(r(),y(g,{key:0})):(r(),y(h,{key:1}))])):L("",!0)])}const Cs=B(rs,[["render",ns],["__scopeId","data-v-cee16cc3"]]);export{Cs as default};
