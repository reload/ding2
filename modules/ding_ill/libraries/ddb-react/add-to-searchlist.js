(window.webpackJsonp=window.webpackJsonp||[]).push([[3],{247:function(e,t,a){"use strict";a.r(t);var r=a(26),n=a(8),s=a.n(n),l=a(0),i=a.n(l),d=(a(2),a(16),a(149)),c=a.n(d),o=a(11),u=a(78),m=(a(29),a(121),a(76)),g=a(79),b=a(77),f=i.a.forwardRef((function(e,t){var a=e.labelClass,r=e.containerClass,n=e.inputClass,s=e.hideLabel,l=e.label,d=e.placeholder,c=e.id,o=e.defaultValue,u=e.name,f=e.value,h=e.required,p=e.onChange,x=e.error,T=Object(m.a)(c);return i.a.createElement(i.a.Fragment,null,i.a.createElement("label",{htmlFor:T,className:"ddb-reset ddb-textfield__label ".concat(s?"ddb-textfield__label--hidden":""," ").concat(a)},l),i.a.createElement("div",{className:"ddb-textfield__container ".concat(r)},i.a.createElement("div",{className:"ddb-textfield__action"},i.a.createElement("input",{id:T,name:u,ref:t,defaultValue:o,type:"text",className:"ddb-reset ddb-textfield__input ".concat(x?"ddb-textfield__input--error":""," ").concat(n),value:f,placeholder:d||l,onChange:p,required:h}),i.a.createElement("div",{className:"ddb-textfield__icon"},x&&i.a.createElement(b.a,{variant:"red"}))),i.a.createElement("div",{className:"ddb-textfield__error"},x&&i.a.createElement(g.a,{type:"polite"},x))))}));f.defaultProps={inputClass:"",containerClass:"",labelClass:"",error:!1,id:void 0,value:void 0,defaultValue:"",placeholder:void 0,onChange:void 0,hideLabel:!0,required:!1};var h=f,p=a(3),x=a(33),T=a(20);var v=function(e){var t,a=e.status,r=e.onSubmit,n=e.buttonText,d=e.labelText,m=e.defaultTitle,g=e.addButtonText,b=e.helpText,f=e.errorText,v=e.successText,_=e.successLinkUrl,E=e.successLinkText,k=e.errorRequiredMessage,N=e.errorMaxLengthMessage,L=e.searchQuery,w=e.loginUrl,C=Object(l.useState)(!1),y=s()(C,2),M=y[0],S=y[1],U=c()(),q=U.register,j=U.handleSubmit,O=U.errors;if("failed"===a)return i.a.createElement(p.a,{message:f,type:"assertive",variant:"warning"});var R=i.a.createElement("div",{className:"ddb-add-to-searchlist__success"},i.a.createElement(p.a,{message:v,type:"polite",variant:"blank"}),_&&i.a.createElement("a",{href:_},E)),B=i.a.createElement("div",{className:"ddb-add-to-searchlist__content"},i.a.createElement("div",{className:"ddb-add-to-searchlist__info"},i.a.createElement("p",{className:"ddb-reset ddb-add-to-searchlist__help"},b)),i.a.createElement("form",{onSubmit:j((function(e,t){t.target.reset(),r(e.title)})),className:"ddb-add-to-searchlist__action"},i.a.createElement(h,{name:"title",ref:q({required:k,maxLength:{value:255,message:N}}),inputClass:"ddb-add-to-searchlist__input",containerClass:"ddb-add-to-searchlist__input-container",label:d,defaultValue:m,error:null==O?void 0:null===(t=O.title)||void 0===t?void 0:t.message}),i.a.createElement(o.a,{type:"submit",className:"ddb-add-to-searchlist__button",variant:"charcoal",align:"left"},g)));return i.a.createElement("section",{className:"ddb-add-to-searchlist"},i.a.createElement(o.a,{href:x.a.isAuthenticated()?void 0:Object(T.a)({text:w,tags:{query:encodeURIComponent(L)}}),className:"ddb-add-to-searchlist__open-dialog-btn",onClick:function(){return S(!0)}},n),i.a.createElement(u.a,{label:"Tilføj søgning til liste",showCloseButton:!0,dropDown:!0,isOpen:M,onDismiss:function(){return S(!1)}},"processing"===a?R:B))},_=a(61);function E(e){var t=e.followSearchesUrl,a=e.searchQuery,r=e.buttonText,n=e.labelText,d=e.defaultTitle,c=e.helpText,o=e.successText,u=e.successLinkUrl,m=e.successLinkText,g=e.errorText,b=e.addButtonText,f=e.errorRequiredMessage,h=e.errorMaxLengthMessage,p=e.loginUrl,x=Object(l.useState)("ready"),T=s()(x,2),E=T[0],k=T[1];return i.a.createElement(v,{status:E,onSubmit:function(e){k("processing"),new _.a({baseUrl:t}).addSearch({title:e,query:a}).then((function(){setTimeout((function(){k("ready")}),1e4)})).catch((function(){k("failed"),setTimeout((function(){k("ready")}),2e3)}))},searchQuery:a,buttonText:r,labelText:n,defaultTitle:d,errorText:g,successText:o,successLinkUrl:u,successLinkText:m,addButtonText:b,helpText:c,errorRequiredMessage:f,errorMaxLengthMessage:h,loginUrl:p})}E.defaultProps={followSearchesUrl:"https://stage.followsearches.dandigbib.org",buttonText:"Tilføj til mine søgninger",labelText:"Søgetitel",errorText:"Noget gik galt",successText:"Tilføjet til dine gemte søgninger.",successLinkUrl:void 0,successLinkText:"Se dine gemte søgnigner.",errorRequiredMessage:"En titel er påkrævet.",errorMaxLengthMessage:"Titlen må ikke være længere end 255 tegn.",addButtonText:"Gem",defaultTitle:"",helpText:"Gem en søgning her og giv den en titel så du nemt kan kende forskel på alle dine mange gemte søgninger."};var k=E;Object(r.a)({appName:"add-to-searchlist",app:k})}},[[247,1,0]]]);
//# sourceMappingURL=add-to-searchlist.js.map