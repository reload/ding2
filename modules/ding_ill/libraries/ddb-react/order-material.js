(window.webpackJsonp=window.webpackJsonp||[]).push([[5],{264:function(e,t,n){"use strict";n.r(t);var a=n(23),r=(n(152),n(5)),i=n.n(r),s=n(0),c=n.n(s),l=(n(2),n(14),n(19)),o=n(82),u=n(7),p=n(37),f=n(11);function d(e){var t=e.status,n=e.checkingText,a=e.progressText,r=e.unavailableText,d=e.invalidPickupBranchText,h=e.onClick,g=e.text,b=e.errorText,v=e.successText,m=e.successMessage,k=e.loginUrl,x=e.materialIds,y=Object(s.useState)(!0),w=i()(y,2),T=w[0],O=w[1];switch(t){case"initial":return null;case"checking":return c.a.createElement(u.a,{message:n,type:"polite",variant:"info"});case"unavailable":return c.a.createElement(u.a,{message:r,type:"polite",variant:"info"});case"invalid branch":return c.a.createElement(u.a,{message:d,type:"polite",variant:"warning"});case"processing":return c.a.createElement(u.a,{message:a,type:"polite",variant:"info"});case"finished":return c.a.createElement(c.a.Fragment,null,c.a.createElement(u.a,{message:v,type:"polite",variant:"success"}),c.a.createElement(o.a,{label:"Tilføj søgning til liste",showCloseButton:!0,dropDown:!0,isOpen:T,onDismiss:function(){return O(!1)}},c.a.createElement(u.a,{message:m,type:"polite",variant:"blank"})));case"failed":return c.a.createElement(u.a,{message:b,type:"polite",variant:"warning"});default:return c.a.createElement("div",{className:"ddb-order-material__container"},c.a.createElement(l.a,{href:p.a.isAuthenticated()?void 0:Object(f.a)({text:k,tags:{id:encodeURIComponent(x.first)}}),variant:"black",align:"left",onClick:h},g))}}d.defaultProps={status:"initial"};var h=d,g=n(45),b=(n(38),n(22),n(28),n(50),n(41),n(42),n(35),n(43),n(1)),v=n.n(b),m=n(15),k=n.n(m),x=(n(44),n(3)),y=n.n(x),w=n(17),T=n.n(w),O=n(18),j=n.n(O),E=n(12);function P(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function B(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?P(n,!0).forEach((function(t){k()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):P(n).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var D=function(){function e(t){T()(this,e),this.baseUrl=t}var t,n;return j()(e,[{key:"request",value:(n=y()(v.a.mark((function e(t){var n,a,r,i=arguments;return v.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n=i.length>1&&void 0!==i[1]?i[1]:{},a={headers:{Accept:"application/json"}},e.next=4,Object(E.a)("".concat(this.baseUrl,"/").concat(t),B({},a,{},n));case 4:return r=e.sent,e.abrupt("return",r.json());case 6:case"end":return e.stop()}}),e,this)}))),function(e){return n.apply(this,arguments)})},{key:"isAvailableForIll",value:(t=y()(v.a.mark((function e(t){return v.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",this.request(t.join(",")).then((function(e){return e.canBeOrdered})));case 1:case"end":return e.stop()}}),e,this)}))),function(e){return t.apply(this,arguments)})}]),e}();function U(e){return"string"==typeof e?e.split(","):e}function M(e){var t=e.text,n=e.successText,a=e.successMessage,r=e.errorText,l=e.checkingText,o=e.progressText,u=e.unavailableText,f=e.invalidPickupBranchText,d=e.ids,b=e.loginUrl,v=e.illCheckUrl,m=e.pickupBranch,k=e.expires,x=Object(s.useState)("initial"),y=i()(x,2),w=y[0],T=y[1];return Object(s.useEffect)((function(){var e=new g.a,t=new D(v);T("checking"),t.isAvailableForIll(U(d)).then((function(t){t?p.a.isAuthenticated()?e.getBranch(m).then((function(e){"1"!==e.willReceiveIll?T("invalid branch"):T("ready")})).catch((function(){T("failed")})):T("ready"):T("unavailable")})).catch((function(){T("failed")}))}),[d,m,v]),c.a.createElement(h,{text:t,errorText:r,successText:n,successMessage:a,checkingText:l,progressText:o,unavailableText:u,invalidPickupBranchText:f,status:w,onClick:function(){T("processing"),(new g.a).orderMaterial({pids:U(d),pickupBranch:m,expires:k}).then((function(){T("finished")})).catch((function(){T("failed")}))},loginUrl:b,materialIds:U(d)})}M.defaultProps={text:"Bestil materiale",checkingText:"Undersøger mulighed for fjernlån",progressText:"Bestiller materiale",unavailableText:"Kan ikke fjernlånes",invalidPickupBranchText:"Dit afhentningsbibliotek modtager ikke fjernlån",errorText:"Det lykkedes ikke at bestille materialet.",successText:"Materialet er bestilt",successMessage:"Materialet er bestilt, dit bibliotek vil give besked når det er klar til afhentning."};var C=M;Object(a.a)({appName:"order-material",app:C})}},[[264,1,0]]]);
//# sourceMappingURL=order-material.js.map