(window.webpackJsonp=window.webpackJsonp||[]).push([[2],{264:function(e,t,a){"use strict";a.r(t);var r=a(20),i=a(4),s=a.n(i),n=a(0),l=a.n(n),c=(a(2),a(13),a(15)),o=a(7),d=a(36),u=a(11);function m(e){var t=e.status,a=e.onClick,r=e.text,i=e.errorText,s=e.successText,n=e.loginUrl,m=e.materialId;return"processing"===t?l.a.createElement(o.a,{message:s,type:"polite",variant:"success"}):"failed"===t?l.a.createElement(o.a,{message:i,type:"polite",variant:"warning"}):l.a.createElement("div",{className:"ddb-add-to-checklist__container"},l.a.createElement(c.a,{href:d.a.isAuthenticated()?void 0:Object(u.a)({text:n,tags:{id:encodeURIComponent(m)}}),variant:"black",align:"left",onClick:a},r))}m.defaultProps={status:"ready"};var p=m,f=a(63);function g(e){var t=e.materialListUrl,a=e.text,r=e.successText,i=e.errorText,c=e.id,o=e.loginUrl,d=Object(n.useState)("ready"),u=s()(d,2),m=u[0],g=u[1];function x(){g("ready")}function k(){g("failed"),setTimeout(x,4e3)}return l.a.createElement(p,{text:a,errorText:i,successText:r,status:m,onClick:function(){g("processing"),new f.a({baseUrl:t}).addListMaterial({materialId:c}).catch(k)},loginUrl:o,materialId:c})}g.defaultProps={materialListUrl:"https://test.materiallist.dandigbib.org",text:"Tilføj til min liste",errorText:"Det lykkedes ikke at gemme materialet.",successText:"Materialet er tilføjet"};var x=g;Object(r.a)({appName:"add-to-checklist",app:x})}},[[264,1,0]]]);
//# sourceMappingURL=add-to-checklist.js.map