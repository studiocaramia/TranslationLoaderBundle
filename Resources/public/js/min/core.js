window.log=function(){log.history=log.history||[],log.history.push(arguments),console&&console.log&&"dev"==asm.options.env&&console.log(Array.prototype.slice.call(arguments))},function(o,n){window.asm=window.asm||{},asm.translations=asm.translations||{key:"value"},asm.options=asm.options||{locale:"de_DE",env:"prod"},o.extend(asm.options,{test:!1}),asm.buildOnLoad={init:function(){},testFunction:function(){}},asm.fire={message:function(o,n,e){asm.utility.consoleEnabled()&&(n=n?n:"",""!=e&&(e="color: "+e+";"),"string"!=typeof o?console.log("%c data: ",e+"font-family:Arial, Mono; font-size:13px;",o):console.log("%c "+o,e+"font-family: Arial, Mono; font-size:13px;",n))}},asm.log=function(o,n){o=2===arguments.length?o+": ":o,asm.fire.message(o,n,"")},asm.debug=function(o,n){asm.fire.message(o,n,"green")},asm.info=function(o,n){asm.fire.message(o,n,"#00F")},asm.warn=function(o,n){asm.fire.message(o,n,"#FFA500")},asm.error=function(o,n){asm.fire.message(o,n,"Orangered")},asm.start=function(o,n){asm.fire.message(o,n,"#0F0")},asm.end=function(o,n){asm.fire.message(o,n,"#F00")},asm.group=function(o){asm.utility.consoleEnabled()&&console.group(o)},asm.groupEnd=function(){asm.utility.consoleEnabled()&&console.groupEnd()},asm.profile=function(o){asm.utility.consoleEnabled()&&(o?console.profile(o):console.profile())},asm.profileEnd=function(o){asm.utility.consoleEnabled()&&console.profileEnd(o?"End Profile: "+o:"End Profile")},asm.utility={documentReady:function(n){o(document).ready(function(){})},isDev:function(){return"dev"==asm.options.env},consoleEnabled:function(){return asm.utility.isDev()&&window.console},getBaseUrl:function(n){var e=o.extend({forceHttps:!1},n);try{var t=location.protocol;return 1==e.forceHttps&&(t="https:"),t+"//"+location.hostname}catch(s){asm.errorHandler.logError(s)}}},asm.errorHandler={catchError:function(o){return function(){try{return o.apply(this,arguments)}catch(n){asm.errorHandler.logError(n)}}},logError:function(o){console.log("error: "+o)}},asm.renderMustache=function(n,e,t){o.getJSON(n,function(s){s.length>0?(o(e).html(Mustache.render(o(t).html(),s)),asm.log("mustache::refreshed "+t)):asm.log("mustache::no elements found for "+n)})},asm.modal={defaultOptions:{url:"",method:"GET",success:function(){},onLoad:function(){},onClose:function(){},modal:!0,selfClose:!1,width:680,height:"auto",resizable:!1,modalClass:null,closeText:"",closeOnEscape:!0,data:null,showClose:!0},leaveOutTimerId:0,init:function(n){n&&"object"==typeof n&&(asm.modal.options=o.extend({},asm.modal.defaultOptions,n)),this._openModal()},close:function(){o(this).dialog("destroy")},_openModal:function(){function e(e){var i=o("#asm-dialog").dialog({modal:t.options.modal,autoOpen:t.options.autoOpen,width:t.options.width,height:t.options.height,resizable:t.options.resizable,closeText:t.options.closeText,closeOnEscape:t.options.closeOnEscape,show:{effect:"fadeIn",duration:800},close:function(){o(this).dialog("destroy"),o("#asm-dialog").children(".content").empty()}});s.empty(),s.append(e),i.dialog("open"),asm.log("created dialog"),o(".ui-widget-overlay.ui-front").on("click",function(){i.dialog("destroy"),o("#asm-dialog").children(".content").empty()}),t.options.success!==n&&t.options.success(e),t.options&&"function"==typeof t.options.onLoad&&t.options.onLoad(self)}var t=this,s=o("#asm-dialog").children(".content");"undefined"!=typeof t.options.url&&""!=t.options.url?o.ajax({url:t.options.url,type:t.options.method}).done(function(o){e(o)}):e(t.options.data)}}}(jQuery),asm.buildOnLoad.init();
!function(e,t){"object"==typeof exports&&exports?t(exports):"function"==typeof define&&define.amd?define(["exports"],t):t(e.Mustache={})}(this,function(e){function t(e){return"function"==typeof e}function n(e){return e.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}function r(e,t){return g.call(e,t)}function i(e){return!r(w,e)}function s(e){return String(e).replace(/[&<>"'\/]/g,function(e){return d[e]})}function o(t,r){function s(){if(U&&!m)for(;d.length;)delete w[d.pop()];else d=[];U=!1,m=!1}function o(e){if("string"==typeof e&&(e=e.split(k,2)),!f(e)||2!==e.length)throw new Error("Invalid tags: "+e);h=new RegExp(n(e[0])+"\\s*"),l=new RegExp("\\s*"+n(e[1])),p=new RegExp("\\s*"+n("}"+e[1]))}if(!t)return[];var h,l,p,g=[],w=[],d=[],U=!1,m=!1;o(r||e.tags);for(var E,T,j,C,A,R,S=new u(t);!S.eos();){if(E=S.pos,j=S.scanUntil(h))for(var O=0,$=j.length;$>O;++O)C=j.charAt(O),i(C)?d.push(w.length):m=!0,w.push(["text",C,E,E+1]),E+=1,"\n"===C&&s();if(!S.scan(h))break;if(U=!0,T=S.scan(x)||"name",S.scan(v),"="===T?(j=S.scanUntil(y),S.scan(y),S.scanUntil(l)):"{"===T?(j=S.scanUntil(p),S.scan(b),S.scanUntil(l),T="&"):j=S.scanUntil(l),!S.scan(l))throw new Error("Unclosed tag at "+S.pos);if(A=[T,j,E,S.pos],w.push(A),"#"===T||"^"===T)g.push(A);else if("/"===T){if(R=g.pop(),!R)throw new Error('Unopened section "'+j+'" at '+E);if(R[1]!==j)throw new Error('Unclosed section "'+R[1]+'" at '+E)}else"name"===T||"{"===T||"&"===T?m=!0:"="===T&&o(j)}if(R=g.pop())throw new Error('Unclosed section "'+R[1]+'" at '+S.pos);return c(a(w))}function a(e){for(var t,n,r=[],i=0,s=e.length;s>i;++i)t=e[i],t&&("text"===t[0]&&n&&"text"===n[0]?(n[1]+=t[1],n[3]=t[3]):(r.push(t),n=t));return r}function c(e){for(var t,n,r=[],i=r,s=[],o=0,a=e.length;a>o;++o)switch(t=e[o],t[0]){case"#":case"^":i.push(t),s.push(t),i=t[4]=[];break;case"/":n=s.pop(),n[5]=t[2],i=s.length>0?s[s.length-1][4]:r;break;default:i.push(t)}return r}function u(e){this.string=e,this.tail=e,this.pos=0}function h(e,t){this.view=null==e?{}:e,this.cache={".":this.view},this.parent=t}function l(){this.cache={}}var p=Object.prototype.toString,f=Array.isArray||function(e){return"[object Array]"===p.call(e)},g=RegExp.prototype.test,w=/\S/,d={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"},v=/\s*/,k=/\s+/,y=/\s*=/,b=/\s*\}/,x=/#|\^|\/|>|\{|&|=|!/;u.prototype.eos=function(){return""===this.tail},u.prototype.scan=function(e){var t=this.tail.match(e);if(!t||0!==t.index)return"";var n=t[0];return this.tail=this.tail.substring(n.length),this.pos+=n.length,n},u.prototype.scanUntil=function(e){var t,n=this.tail.search(e);switch(n){case-1:t=this.tail,this.tail="";break;case 0:t="";break;default:t=this.tail.substring(0,n),this.tail=this.tail.substring(n)}return this.pos+=t.length,t},h.prototype.push=function(e){return new h(e,this)},h.prototype.lookup=function(e){var n,r=this.cache;if(e in r)n=r[e];else{for(var i,s,o=this;o;){if(e.indexOf(".")>0)for(n=o.view,i=e.split("."),s=0;null!=n&&s<i.length;)n=n[i[s++]];else n=o.view[e];if(null!=n)break;o=o.parent}r[e]=n}return t(n)&&(n=n.call(this.view)),n},l.prototype.clearCache=function(){this.cache={}},l.prototype.parse=function(e,t){var n=this.cache,r=n[e];return null==r&&(r=n[e]=o(e,t)),r},l.prototype.render=function(e,t,n){var r=this.parse(e),i=t instanceof h?t:new h(t);return this.renderTokens(r,i,n,e)},l.prototype.renderTokens=function(n,r,i,s){function o(e){return h.render(e,r,i)}for(var a,c,u="",h=this,l=0,p=n.length;p>l;++l)switch(a=n[l],a[0]){case"#":if(c=r.lookup(a[1]),!c)continue;if(f(c))for(var g=0,w=c.length;w>g;++g)u+=this.renderTokens(a[4],r.push(c[g]),i,s);else if("object"==typeof c||"string"==typeof c)u+=this.renderTokens(a[4],r.push(c),i,s);else if(t(c)){if("string"!=typeof s)throw new Error("Cannot use higher-order sections without the original template");c=c.call(r.view,s.slice(a[3],a[5]),o),null!=c&&(u+=c)}else u+=this.renderTokens(a[4],r,i,s);break;case"^":c=r.lookup(a[1]),(!c||f(c)&&0===c.length)&&(u+=this.renderTokens(a[4],r,i,s));break;case">":if(!i)continue;c=t(i)?i(a[1]):i[a[1]],null!=c&&(u+=this.renderTokens(this.parse(c),r,i,c));break;case"&":c=r.lookup(a[1]),null!=c&&(u+=c);break;case"name":c=r.lookup(a[1]),null!=c&&(u+=e.escape(c));break;case"text":u+=a[1]}return u},e.name="mustache.js",e.version="0.8.1",e.tags=["{{","}}"];var U=new l;e.clearCache=function(){return U.clearCache()},e.parse=function(e,t){return U.parse(e,t)},e.render=function(e,t,n){return U.render(e,t,n)},e.to_html=function(n,r,i,s){var o=e.render(n,r,i);return t(s)?void s(o):o},e.escape=s,e.Scanner=u,e.Context=h,e.Writer=l});
!function(a){a.fn.ajaxForm=function(e){var t=a.extend({action:"",method:"",replaceWithData:!0,animateLoad:!0,onFinish:null},e);try{return this.submit(function(e){e.preventDefault();var o=a(this);""==t.action&&(t.action=o.attr("action")),""==t.method&&(t.method=o.attr("method")),1==t.animateLoad&&o.ajaxAnimateLoad(),a.ajax({type:t.method,url:t.action,data:o.serialize(),success:function(a,e){asm.log("form::response: "+e),1==t.replaceWithData&&o.replaceWith(a),"function"==typeof t.onFinish&&t.onFinish(self)},error:function(a,e,n){asm.log("form::response: "+n),o.replaceWith(a),o.ajaxForm(t)}})}),!1}catch(o){asm.log(o)}}}(jQuery),function(a){a.fn.ajaxAnimateLoad=function(e){var t=a.extend({loaderImage:"/img/ajax-loader.gif",loaderWidth:"32px",loaderHeight:"32px",fadeDuration:200,action:"start",backgroundDisabled:!1},e);return this.each(function(){var e=a(this),o='<span id="ajaxloader" style="display: block; width: '+t.loaderWidth+"; height: "+t.loaderHeight+"; background: transparent url("+t.loaderImage+') no-repeat center center; position: absolute; top: 50%; left: 50%;">&nbsp;</span>',n='<div class="modalBackgroundOverlay" style="position: fixed; width:100%; height: 100%; top: 0px; left: 0px; zoom: 1; opacity: 0.0; background-color: #FFF; z-index: 201;">&nbsp;</div>';"start"==t.action?1==t.backgroundDisabled?(0==e.children(".modalBackgroundOverlay").length&&(e.append(n),jQuery(".modalBackgroundOverlay").animate({opacity:.4},t.fadeDuration)),0==e.children("#ajaxloader").length&&e.append(o)):e.attr("style","position: relative;").append(o).animate({opacity:.4},t.fadeDuration):"stop"==t.action&&(e.children(".modalBackgroundOverlay").length>0?(e.children("#ajaxloader").remove(),e.children(".modalBackgroundOverlay").animate({opacity:0},t.fadeDuration).remove()):e.attr("style","position: static;").remove("#ajaxloader").animate({opacity:1},t.fadeDuration))})}}(jQuery),function(a){a.fn.ajaxLoadElm=function(e,t){var o=a.extend({source:"",animateLoad:!0,backgroundDisabled:!1},e),n=a(this);try{return 1==o.animateLoad&&n.ajaxAnimateLoad({backgroundDisabled:o.backgroundDisabled}),a.get(o.source,function(a){n.replaceWith(a)}),"function"==typeof t&&t.call(this),!1}catch(r){asm.log(r)}}}(jQuery),function(a){a.fn.renderMustache=function(e){var t=a.extend({source:"",template:""},e),o=a(this);""==t.source&&(t.url=o.data("source")),""==t.template&&(t.template=o.data("template")),a.getJSON(t.source,function(e){e.length>0?(o.html(Mustache.render(a(t.template).html(),e)),asm.log("mustache::refreshed "+t.template)):asm.log("mustache::no elements found for "+t.source)})}}(jQuery);
!function(){window.asm["default"]=window.asm["default"]||{},asm["default"]={baseUrl:asm.utility.getBaseUrl(),init:function(){asm.info("asm.default init")}}}(jQuery),asm.utility.documentReady(asm["default"].init());
!function(t){window.asm.list=window.asm.list||{},asm.list={baseUrl:asm.utility.getBaseUrl(),init:function(){asm.info("asm.list init"),t(".asm-edit-btn").length>0&&asm.list.initEditButtons(),t(".asm-delete-btn").length>0&&asm.list.initDeleteButtons(),t(".asm-add-btn").length>0&&asm.list.initAddButton()},initEditButtons:function(){t(".asm-edit-btn").click(function(){var i=t(this).data("key"),a=t(this).data("locale"),n=t(this).data("domain"),s=t(this).data("link");s=encodeURI(s+"/"+i+"/"+a+"/"+n),asm.debug("formUrl: "+s),asm.modal.init({url:s,onClose:function(i){t("#asm-translation-list").reloadList(i)},success:function(){t("#asm-translation-form").ajaxForm()}})})},initAddButton:function(){t(".asm-add-btn").click(function(){var i=t(this).data("link");asm.debug("formUrl: "+i),asm.modal.init({url:i,width:500,resizable:!0,onClose:function(t){asm.list.reloadList(t)}})})},initDeleteButtons:function(){t(".asm-delete-btn").click(function(){var i=confirm(asm.translations.confirm_delete);if(1==i){var a=t(this).data("key"),n=t(this).data("locale"),s=t(this).data("domain");asm.debug("key: "+a+" locale: "+n+" domain: "+s),asm.debug("delete confirmed")}else asm.debug("delete cancelled")})},reloadList:function(i){asm.debug("fired reload"),t("#asm-translations-tbl").renderMustache({source:i+"/list",template:"#asm-translations-tbl-tpl"})}}}(jQuery),asm.utility.documentReady(asm.list.init());
//# sourceMappingURL=core.js.map