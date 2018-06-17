/** ScrollSpy - UIkit 2.3.1 | http://www.getuikit.com | (c) 2014 YOOtheme | MIT License **/

(function(t,e,i){"use strict";var n=t.UIkit||{},o=t("html"),s=t(window);n.fn||(n.version="2.3.1",n.fn=function(e,i){var o=arguments,s=e.match(/^([a-z\-]+)(?:\.([a-z]+))?/i),a=s[1],r=s[2];return n[a]?this.each(function(){var e=t(this),s=e.data(a);s||e.data(a,s=new n[a](this,r?void 0:i)),r&&s[r].apply(s,Array.prototype.slice.call(o,1))}):(t.error("UIkit component ["+a+"] does not exist."),this)},n.support={},n.support.transition=function(){var t=function(){var t,i=e.body||e.documentElement,n={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(t in n)if(void 0!==i.style[t])return n[t]}();return t&&{end:t}}(),n.support.requestAnimationFrame=i.requestAnimationFrame||i.webkitRequestAnimationFrame||i.mozRequestAnimationFrame||i.msRequestAnimationFrame||i.oRequestAnimationFrame||function(t){i.setTimeout(t,1e3/60)},n.support.touch="ontouchstart"in window&&navigator.userAgent.toLowerCase().match(/mobile|tablet/)||i.DocumentTouch&&document instanceof i.DocumentTouch||i.navigator.msPointerEnabled&&i.navigator.msMaxTouchPoints>0||i.navigator.pointerEnabled&&i.navigator.maxTouchPoints>0||!1,n.support.mutationobserver=i.MutationObserver||i.WebKitMutationObserver||i.MozMutationObserver||null,n.Utils={},n.Utils.debounce=function(t,e,i){var n;return function(){var o=this,s=arguments,a=function(){n=null,i||t.apply(o,s)},r=i&&!n;clearTimeout(n),n=setTimeout(a,e),r&&t.apply(o,s)}},n.Utils.removeCssRules=function(t){var e,i,n,o,s,a,r,l,u,c;t&&setTimeout(function(){try{for(c=document.styleSheets,o=0,r=c.length;r>o;o++){for(n=c[o],i=[],n.cssRules=n.cssRules,e=s=0,l=n.cssRules.length;l>s;e=++s)n.cssRules[e].type===CSSRule.STYLE_RULE&&t.test(n.cssRules[e].selectorText)&&i.unshift(e);for(a=0,u=i.length;u>a;a++)n.deleteRule(i[a])}}catch(d){}},0)},n.Utils.isInView=function(e,i){var n=t(e);if(!n.is(":visible"))return!1;var o=s.scrollLeft(),a=s.scrollTop(),r=n.offset(),l=r.left,u=r.top;return i=t.extend({topoffset:0,leftoffset:0},i),u+n.height()>=a&&u-i.topoffset<=a+s.height()&&l+n.width()>=o&&l-i.leftoffset<=o+s.width()?!0:!1},n.Utils.options=function(e){if(t.isPlainObject(e))return e;var i=e?e.indexOf("{"):-1,n={};if(-1!=i)try{n=Function("","var json = "+e.substr(i)+"; return JSON.parse(JSON.stringify(json));")()}catch(o){}return n},n.Utils.events={},n.Utils.events.click=n.support.touch?"tap":"click",t.UIkit=n,t.fn.uk=n.fn,t.UIkit.langdirection="rtl"==o.attr("dir")?"right":"left",t(function(){if(t(e).trigger("uk-domready"),n.support.mutationobserver){var i=new n.support.mutationobserver(n.Utils.debounce(function(){t(e).trigger("uk-domready")},300));i.observe(document.body,{childList:!0,subtree:!0}),n.support.touch}}),o.addClass(n.support.touch?"uk-touch":"uk-notouch"))})(jQuery,document,window),function(t,e){"use strict";var i=t(window),n="resize orientationchange",o=function(s,a){var r=this,l=t(s);l.data("stackMargin")||(this.element=l,this.columns=this.element.children(),this.options=t.extend({},o.defaults,a),this.columns.length&&(i.on(n,function(){var n=function(){r.process()};return t(function(){n(),i.on("load",n)}),e.Utils.debounce(n,150)}()),t(document).on("uk-domready",function(){r.columns=r.element.children(),r.process()}),this.element.data("stackMargin",this)))};t.extend(o.prototype,{process:function(){var e=this;this.revert();var i=!1,n=this.columns.filter(":visible:first"),o=n.length?n.offset().top:!1;if(o!==!1)return this.columns.each(function(){var n=t(this);n.is(":visible")&&(i?n.addClass(e.options.cls):n.offset().top!=o&&(n.addClass(e.options.cls),i=!0))}),this},revert:function(){return this.columns.removeClass(this.options.cls),this}}),o.defaults={cls:"uk-margin-small-top"},e.stackMargin=o,t(document).on("uk-domready",function(){t("[data-uk-margin]").each(function(){var i,n=t(this);n.data("stackMargin")||(i=new o(n,e.Utils.options(n.attr("data-uk-margin"))))})})}(jQuery,jQuery.UIkit),function(t){function e(t,e,i,n){return Math.abs(t-e)>=Math.abs(i-n)?t-e>0?"Left":"Right":i-n>0?"Up":"Down"}function i(){u=null,d.last&&(d.el.trigger("longTap"),d={})}function n(){u&&clearTimeout(u),u=null}function o(){a&&clearTimeout(a),r&&clearTimeout(r),l&&clearTimeout(l),u&&clearTimeout(u),a=r=l=u=null,d={}}function s(t){return t.pointerType==t.MSPOINTER_TYPE_TOUCH&&t.isPrimary}var a,r,l,u,c,d={},h=750;t(function(){var f,p,m,g=0,v=0;"MSGesture"in window&&(c=new MSGesture,c.target=document.body),t(document).bind("MSGestureEnd",function(t){var e=t.originalEvent.velocityX>1?"Right":-1>t.originalEvent.velocityX?"Left":t.originalEvent.velocityY>1?"Down":-1>t.originalEvent.velocityY?"Up":null;e&&(d.el.trigger("swipe"),d.el.trigger("swipe"+e))}).on("touchstart MSPointerDown",function(e){("MSPointerDown"!=e.type||s(e.originalEvent))&&(m="MSPointerDown"==e.type?e:e.originalEvent.touches[0],f=Date.now(),p=f-(d.last||f),d.el=t("tagName"in m.target?m.target:m.target.parentNode),a&&clearTimeout(a),d.x1=m.pageX,d.y1=m.pageY,p>0&&250>=p&&(d.isDoubleTap=!0),d.last=f,u=setTimeout(i,h),c&&"MSPointerDown"==e.type&&c.addPointer(e.originalEvent.pointerId))}).on("touchmove MSPointerMove",function(t){("MSPointerMove"!=t.type||s(t.originalEvent))&&(m="MSPointerMove"==t.type?t:t.originalEvent.touches[0],n(),d.x2=m.pageX,d.y2=m.pageY,g+=Math.abs(d.x1-d.x2),v+=Math.abs(d.y1-d.y2))}).on("touchend MSPointerUp",function(i){("MSPointerUp"!=i.type||s(i.originalEvent))&&(n(),d.x2&&Math.abs(d.x1-d.x2)>30||d.y2&&Math.abs(d.y1-d.y2)>30?l=setTimeout(function(){d.el.trigger("swipe"),d.el.trigger("swipe"+e(d.x1,d.x2,d.y1,d.y2)),d={}},0):"last"in d&&(isNaN(g)||30>g&&30>v?r=setTimeout(function(){var e=t.Event("tap");e.cancelTouch=o,d.el.trigger(e),d.isDoubleTap?(d.el.trigger("doubleTap"),d={}):a=setTimeout(function(){a=null,d.el.trigger("singleTap"),d={}},250)},0):d={},g=v=0))}).on("touchcancel MSPointerCancel",o),t(window).on("scroll",o)}),["swipe","swipeLeft","swipeRight","swipeUp","swipeDown","doubleTap","tap","singleTap","longTap"].forEach(function(e){t.fn[e]=function(i){return t(this).on(e,i)}})}(jQuery),function(t,e){"use strict";var i=function(e,n){var o=this;this.options=t.extend({},i.defaults,n),this.element=t(e),this.element.data("alert")||(this.element.on("click",this.options.trigger,function(t){t.preventDefault(),o.close()}),this.element.data("alert",this))};t.extend(i.prototype,{close:function(){function t(){e.trigger("closed").remove()}var e=this.element.trigger("close");this.options.fade?e.css("overflow","hidden").css("max-height",e.height()).animate({height:0,opacity:0,"padding-top":0,"padding-bottom":0,"margin-top":0,"margin-bottom":0},this.options.duration,t):t()}}),i.defaults={fade:!0,duration:200,trigger:".uk-alert-close"},e.alert=i,t(document).on("click.alert.uikit","[data-uk-alert]",function(n){var o=t(this);if(!o.data("alert")){var s=new i(o,e.Utils.options(o.data("uk-alert")));t(n.target).is(o.data("alert").options.trigger)&&(n.preventDefault(),s.close())}})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=function(e,n){var o=this,s=t(e);s.data("buttonRadio")||(this.options=t.extend({},i.defaults,n),this.element=s.on("click",this.options.target,function(e){e.preventDefault(),s.find(o.options.target).not(this).removeClass("uk-active").blur(),s.trigger("change",[t(this).addClass("uk-active")])}),this.element.data("buttonRadio",this))};t.extend(i.prototype,{getSelected:function(){this.element.find(".uk-active")}}),i.defaults={target:".uk-button"};var n=function(e,i){var o=t(e);o.data("buttonCheckbox")||(this.options=t.extend({},n.defaults,i),this.element=o.on("click",this.options.target,function(e){e.preventDefault(),o.trigger("change",[t(this).toggleClass("uk-active").blur()])}),this.element.data("buttonCheckbox",this))};t.extend(n.prototype,{getSelected:function(){this.element.find(".uk-active")}}),n.defaults={target:".uk-button"};var o=function(e,i){var n=this,s=t(e);s.data("button")||(this.options=t.extend({},o.defaults,i),this.element=s.on("click",function(t){t.preventDefault(),n.toggle(),s.trigger("change",[s.blur().hasClass("uk-active")])}),this.element.data("button",this))};t.extend(o.prototype,{options:{},toggle:function(){this.element.toggleClass("uk-active")}}),o.defaults={},e.button=o,e.buttonCheckbox=n,e.buttonRadio=i,t(document).on("click.buttonradio.uikit","[data-uk-button-radio]",function(n){var o=t(this);if(!o.data("buttonRadio")){var s=new i(o,e.Utils.options(o.attr("data-uk-button-radio")));t(n.target).is(s.options.target)&&t(n.target).trigger("click")}}),t(document).on("click.buttoncheckbox.uikit","[data-uk-button-checkbox]",function(i){var o=t(this);if(!o.data("buttonCheckbox")){var s=new n(o,e.Utils.options(o.attr("data-uk-button-checkbox")));t(i.target).is(s.options.target)&&t(i.target).trigger("click")}}),t(document).on("click.button.uikit","[data-uk-button]",function(){var e=t(this);e.data("button")||(new o(e,e.attr("data-uk-button")),e.trigger("click"))})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=!1,n=function(o,s){var a=this,r=t(o);r.data("dropdown")||(this.options=t.extend({},n.defaults,s),this.element=r,this.dropdown=this.element.find(".uk-dropdown"),this.centered=this.dropdown.hasClass("uk-dropdown-center"),this.justified=this.options.justify?t(this.options.justify):!1,this.boundary=t(this.options.boundary),this.boundary.length||(this.boundary=t(window)),"click"==this.options.mode||e.support.touch?this.element.on("click",function(e){var n=t(e.target);n.parents(".uk-dropdown").length||((n.is("a[href='#']")||n.parent().is("a[href='#']"))&&e.preventDefault(),n.blur()),a.element.hasClass("uk-open")?(n.is("a")||!a.element.find(".uk-dropdown").find(e.target).length)&&(a.element.removeClass("uk-open"),i=!1):a.show()}):this.element.on("mouseenter",function(){a.remainIdle&&clearTimeout(a.remainIdle),a.show()}).on("mouseleave",function(){a.remainIdle=setTimeout(function(){a.element.removeClass("uk-open"),a.remainIdle=!1,i&&i[0]==a.element[0]&&(i=!1)},a.options.remaintime)}),this.element.data("dropdown",this))};t.extend(n.prototype,{remainIdle:!1,show:function(){i&&i[0]!=this.element[0]&&i.removeClass("uk-open"),this.checkDimensions(),this.element.addClass("uk-open"),i=this.element,this.registerOuterClick()},registerOuterClick:function(){var e=this;t(document).off("click.outer.dropdown"),setTimeout(function(){t(document).on("click.outer.dropdown",function(n){!i||i[0]!=e.element[0]||!t(n.target).is("a")&&e.element.find(".uk-dropdown").find(n.target).length||(i.removeClass("uk-open"),t(document).off("click.outer.dropdown"))})},10)},checkDimensions:function(){if(this.dropdown.length){var e=this.dropdown.css("margin-"+t.UIkit.langdirection,"").css("min-width",""),i=e.show().offset(),n=e.outerWidth(),o=this.boundary.width(),s=this.boundary.offset()?this.boundary.offset().left:0;if(this.centered&&(e.css("margin-"+t.UIkit.langdirection,-1*(parseFloat(n)/2-e.parent().width()/2)),i=e.offset(),(n+i.left>o||0>i.left)&&(e.css("margin-"+t.UIkit.langdirection,""),i=e.offset())),this.justified&&this.justified.length){var a=this.justified.outerWidth();if(e.css("min-width",a),"right"==t.UIkit.langdirection){var r=o-(this.justified.offset().left+a),l=o-(e.offset().left+e.outerWidth());e.css("margin-right",r-l)}else e.css("margin-left",this.justified.offset().left-i.left);i=e.offset()}n+(i.left-s)>o&&(e.addClass("uk-dropdown-flip"),i=e.offset()),0>i.left&&e.addClass("uk-dropdown-stack"),e.css("display","")}}}),n.defaults={mode:"hover",remaintime:800,justify:!1,boundary:t(window)},e.dropdown=n;var o=e.support.touch?"click":"mouseenter";t(document).on(o+".dropdown.uikit","[data-uk-dropdown]",function(i){var s=t(this);if(!s.data("dropdown")){var a=new n(s,e.Utils.options(s.data("uk-dropdown")));("click"==o||"mouseenter"==o&&"hover"==a.options.mode)&&a.show(),a.element.find(".uk-dropdown").length&&i.preventDefault()}})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=t(window),n="resize orientationchange",o=function(s,a){var r=this,l=t(s);l.data("gridMatchHeight")||(this.options=t.extend({},o.defaults,a),this.element=l,this.columns=this.element.children(),this.elements=this.options.target?this.element.find(this.options.target):this.columns,this.columns.length&&(i.on(n,function(){var n=function(){r.match()};return t(function(){n(),i.on("load",n)}),e.Utils.debounce(n,150)}()),t(document).on("uk-domready",function(){r.columns=r.element.children(),r.elements=r.options.target?r.element.find(r.options.target):r.columns,r.match()}),this.element.data("gridMatchHeight",this)))};t.extend(o.prototype,{match:function(){this.revert();var e=this.columns.filter(":visible:first");if(e.length){var i=Math.ceil(100*parseFloat(e.css("width"))/parseFloat(e.parent().css("width")))>=100?!0:!1,n=0;if(!i)return this.elements.each(function(){n=Math.max(n,t(this).outerHeight())}).each(function(){var e=t(this),i=n-(e.outerHeight()-e.height());e.css("min-height",i+"px")}),this}},revert:function(){return this.elements.css("min-height",""),this}}),o.defaults={target:!1};var s=function(i,n){var o=t(i);if(!o.data("gridMargin")){this.options=t.extend({},s.defaults,n);var a=new e.stackMargin(o,this.options);o.data("gridMargin",a)}};s.defaults={cls:"uk-grid-margin"},e.gridMatchHeight=o,e.gridMargin=s,t(document).on("uk-domready",function(){t("[data-uk-grid-match],[data-uk-grid-margin]").each(function(){var i,n=t(this);n.is("[data-uk-grid-match]")&&!n.data("gridMatchHeight")&&(i=new o(n,e.Utils.options(n.attr("data-uk-grid-match")))),n.is("[data-uk-grid-margin]")&&!n.data("gridMargin")&&(i=new s(n,e.Utils.options(n.attr("data-uk-grid-margin"))))})})}(jQuery,jQuery.UIkit),function(t,e,i){"use strict";function n(e,i){return i?("object"==typeof e?(e=e instanceof jQuery?e:t(e),e.parent().length&&(i.persist=e,i.persist.data("modalPersistParent",e.parent()))):e="string"==typeof e||"number"==typeof e?t("<div></div>").html(e):t("<div></div>").html("$.UIkitt.modal Error: Unsupported data type: "+typeof e),e.appendTo(i.element.find(".uk-modal-dialog")),i):void 0}var o=!1,s=t("html"),a='<div class="uk-modal"><div class="uk-modal-dialog"></div></div>',r=function(i,n){var o=this;this.element=t(i),this.options=t.extend({},r.defaults,n),this.transition=e.support.transition,this.dialog=this.element.find(".uk-modal-dialog"),this.element.on("click",".uk-modal-close",function(t){t.preventDefault(),o.hide()}).on("click",function(e){var i=t(e.target);i[0]==o.element[0]&&o.options.bgclose&&o.hide()})};t.extend(r.prototype,{transition:!1,toggle:function(){return this[this.isActive()?"hide":"show"](),this},show:function(){return this.isActive()?void 0:(o&&o.hide(!0),this.resize(),this.element.removeClass("uk-open").show(),o=this,s.addClass("uk-modal-page").height(),this.element.addClass("uk-open").trigger("uk.modal.show"),this)},hide:function(t){if(this.isActive()){if(!t&&e.support.transition){var i=this;this.element.one(e.support.transition.end,function(){i._hide()}).removeClass("uk-open")}else this._hide();return this}},resize:function(){this.dialog.css("margin-left","");var t=parseInt(this.dialog.css("width"),10),e=t+parseInt(this.dialog.css("margin-left"),10)+parseInt(this.dialog.css("margin-right"),10)<i.width();this.dialog.css("margin-left",t&&e?-1*Math.ceil(t/2):"")},_hide:function(){this.element.hide().removeClass("uk-open"),s.removeClass("uk-modal-page"),o===this&&(o=!1),this.element.trigger("uk.modal.hide")},isActive:function(){return o==this}}),r.defaults={keyboard:!0,show:!1,bgclose:!0};var l=function(e,i){var n=this,o=t(e);o.data("modal")||(this.options=t.extend({target:o.is("a")?o.attr("href"):!1},i),this.element=o,this.modal=new r(this.options.target,i),o.on("click",function(t){t.preventDefault(),n.show()}),t.each(["show","hide","isActive"],function(t,e){n[e]=function(){return n.modal[e]()}}),this.element.data("modal",this))};l.dialog=function(e,i){var o=new r(t(a).appendTo("body"),i);return o.element.on("uk.modal.hide",function(){o.persist&&(o.persist.appendTo(o.persist.data("modalPersistParent")),o.persist=!1),o.element.remove()}),n(e,o),o},l.alert=function(e,i){l.dialog(['<div class="uk-margin">'+(e+"")+"</div>",'<button class="uk-button uk-button-primary uk-modal-close">Ok</button>'].join(""),t.extend({bgclose:!1,keyboard:!1},i)).show()},l.confirm=function(e,i,n){i=t.isFunction(i)?i:function(){};var o=l.dialog(['<div class="uk-margin">'+(e+"")+"</div>",'<button class="uk-button uk-button-primary js-modal-confirm">Ok</button> <button class="uk-button uk-modal-close">Cancel</button>'].join(""),t.extend({bgclose:!1,keyboard:!1},n));o.element.find(".js-modal-confirm").on("click",function(){i(),o.hide()}),o.show()},l.Modal=r,e.modal=l,t(document).on("click.modal.uikit","[data-uk-modal]",function(){var i=t(this);if(!i.data("modal")){var n=new l(i,e.Utils.options(i.attr("data-uk-modal")));n.show()}}),t(document).on("keydown.modal.uikit",function(t){o&&27===t.keyCode&&o.options.keyboard&&(t.preventDefault(),o.hide())}),i.on("resize orientationchange",e.Utils.debounce(function(){o&&o.resize()},150))}(jQuery,jQuery.UIkit,jQuery(window)),function(t,e){"use strict";var i,n=t(window),o=t(document),s={show:function(e){if(e=t(e),e.length){var a=t("html"),r=e.find(".uk-offcanvas-bar:first"),l="right"==t.UIkit.langdirection,u=(r.hasClass("uk-offcanvas-bar-flip")?-1:1)*(l?-1:1),c=-1==u&&n.width()<window.innerWidth?window.innerWidth-n.width():0;i={x:window.scrollX,y:window.scrollY},e.addClass("uk-active"),a.css({width:window.innerWidth,height:window.innerHeight}).addClass("uk-offcanvas-page"),a.css(l?"margin-right":"margin-left",(l?-1:1)*(r.outerWidth()-c)*u).width(),r.addClass("uk-offcanvas-bar-show").width(),e.off(".ukoffcanvas").on("click.ukoffcanvas swipeRight.ukoffcanvas swipeLeft.ukoffcanvas",function(e){var i=t(e.target);if(!e.type.match(/swipe/)){if(i.hasClass("uk-offcanvas-bar"))return;if(i.parents(".uk-offcanvas-bar:first").length)return}e.stopImmediatePropagation(),s.hide()}),o.on("keydown.offcanvas",function(t){27===t.keyCode&&s.hide()})}},hide:function(e){var n=t("html"),s=t(".uk-offcanvas.uk-active"),a="right"==t.UIkit.langdirection,r=s.find(".uk-offcanvas-bar:first");s.length&&(t.UIkit.support.transition&&!e?(n.one(t.UIkit.support.transition.end,function(){n.removeClass("uk-offcanvas-page").attr("style",""),s.removeClass("uk-active"),window.scrollTo(i.x,i.y)}).css(a?"margin-right":"margin-left",""),setTimeout(function(){r.removeClass("uk-offcanvas-bar-show")},50)):(n.removeClass("uk-offcanvas-page").attr("style",""),s.removeClass("uk-active"),r.removeClass("uk-offcanvas-bar-show"),window.scrollTo(i.x,i.y)),s.off(".ukoffcanvas"),o.off(".ukoffcanvas"))}},a=function(e,i){var n=this,o=t(e);o.data("offcanvas")||(this.options=t.extend({target:o.is("a")?o.attr("href"):!1},i),this.element=o,o.on("click",function(t){t.preventDefault(),s.show(n.options.target)}),this.element.data("offcanvas",this))};a.offcanvas=s,e.offcanvas=a,o.on("click.offcanvas.uikit","[data-uk-offcanvas]",function(i){i.preventDefault();var n=t(this);n.data("offcanvas")||(new a(n,e.Utils.options(n.attr("data-uk-offcanvas"))),n.trigger("click"))})}(jQuery,jQuery.UIkit),function(t,e){"use strict";function i(e){var i=t(e),n="auto";if(i.is(":visible"))n=i.outerHeight();else{var o={position:i.css("position"),visibility:i.css("visibility"),display:i.css("display")};n=i.css({position:"absolute",visibility:"hidden",display:"block"}).outerHeight(),i.css(o)}return n}var n=function(e,i){var o=this,s=t(e);s.data("nav")||(this.options=t.extend({},n.defaults,i),this.element=s.on("click",this.options.toggler,function(e){e.preventDefault();var i=t(this);o.open(i.parent()[0]==o.element[0]?i:i.parent("li"))}),this.element.find(this.options.lists).each(function(){var e=t(this),i=e.parent(),n=i.hasClass("uk-active");e.wrap('<div style="overflow:hidden;height:0;position:relative;"></div>'),i.data("list-container",e.parent()),n&&o.open(i,!0)}),this.element.data("nav",this))};t.extend(n.prototype,{open:function(e,n){var o=this.element,s=t(e);this.options.multiple||o.children(".uk-open").not(e).each(function(){t(this).data("list-container")&&t(this).data("list-container").stop().animate({height:0},function(){t(this).parent().removeClass("uk-open")})}),s.toggleClass("uk-open"),s.data("list-container")&&(n?s.data("list-container").stop().height(s.hasClass("uk-open")?"auto":0):s.data("list-container").stop().animate({height:s.hasClass("uk-open")?i(s.data("list-container").find("ul:first")):0}))}}),n.defaults={toggler:">li.uk-parent > a[href='#']",lists:">li.uk-parent > ul",multiple:!1},e.nav=n,t(document).on("uk-domready",function(){t("[data-uk-nav]").each(function(){var i=t(this);i.data("nav")||new n(i,e.Utils.options(i.attr("data-uk-nav")))})})}(jQuery,jQuery.UIkit),function(t,e,i){"use strict";var n,o,s=function(e,i){var n=this,o=t(e);o.data("tooltip")||(this.options=t.extend({},s.defaults,i),this.element=o.on({focus:function(){n.show()},blur:function(){n.hide()},mouseenter:function(){n.show()},mouseleave:function(){n.hide()}}),this.tip="function"==typeof this.options.src?this.options.src.call(this.element):this.options.src,this.element.attr("data-cached-title",this.element.attr("title")).attr("title",""),this.element.data("tooltip",this))};t.extend(s.prototype,{tip:"",show:function(){if(o&&clearTimeout(o),this.tip.length){n.stop().css({top:-2e3,visibility:"hidden"}).show(),n.html('<div class="uk-tooltip-inner">'+this.tip+"</div>");var e=this,i=t.extend({},this.element.offset(),{width:this.element[0].offsetWidth,height:this.element[0].offsetHeight}),s=n[0].offsetWidth,a=n[0].offsetHeight,r="function"==typeof this.options.offset?this.options.offset.call(this.element):this.options.offset,l="function"==typeof this.options.pos?this.options.pos.call(this.element):this.options.pos,u={display:"none",visibility:"visible",top:i.top+i.height+a,left:i.left},c=l.split("-");"left"!=c[0]&&"right"!=c[0]||"right"!=t.UIkit.langdirection||(c[0]="left"==c[0]?"right":"left");var d={bottom:{top:i.top+i.height+r,left:i.left+i.width/2-s/2},top:{top:i.top-a-r,left:i.left+i.width/2-s/2},left:{top:i.top+i.height/2-a/2,left:i.left-s-r},right:{top:i.top+i.height/2-a/2,left:i.left+i.width+r}};t.extend(u,d[c[0]]),2==c.length&&(u.left="left"==c[1]?i.left:i.left+i.width-s);var h=this.checkBoundary(u.left,u.top,s,a);if(h){switch(h){case"x":l=2==c.length?c[0]+"-"+(0>u.left?"left":"right"):0>u.left?"right":"left";break;case"y":l=2==c.length?(0>u.top?"bottom":"top")+"-"+c[1]:0>u.top?"bottom":"top";break;case"xy":l=2==c.length?(0>u.top?"bottom":"top")+"-"+(0>u.left?"left":"right"):0>u.left?"right":"left"}c=l.split("-"),t.extend(u,d[c[0]]),2==c.length&&(u.left="left"==c[1]?i.left:i.left+i.width-s)}u.left-=t("body").position().left,o=setTimeout(function(){n.css(u).attr("class","uk-tooltip uk-tooltip-"+l),e.options.animation?n.css({opacity:0,display:"block"}).animate({opacity:1},parseInt(e.options.animation,10)||400):n.show(),o=!1},parseInt(this.options.delay,10)||0)}},hide:function(){this.element.is("input")&&this.element[0]===document.activeElement||(o&&clearTimeout(o),n.stop(),this.options.animation?n.fadeOut(parseInt(this.options.animation,10)||400):n.hide())},content:function(){return this.tip},checkBoundary:function(t,e,n,o){var s="";return(0>t||t-i.scrollLeft()+n>window.innerWidth)&&(s+="x"),(0>e||e-i.scrollTop()+o>window.innerHeight)&&(s+="y"),s}}),s.defaults={offset:5,pos:"top",animation:!1,delay:0,src:function(){return this.attr("title")}},e.tooltip=s,t(function(){n=t('<div class="uk-tooltip"></div>').appendTo("body")}),t(document).on("mouseenter.tooltip.uikit focus.tooltip.uikit","[data-uk-tooltip]",function(){var i=t(this);i.data("tooltip")||(new s(i,e.Utils.options(i.attr("data-uk-tooltip"))),i.trigger("mouseenter"))})}(jQuery,jQuery.UIkit,jQuery(window)),function(t,e){"use strict";var i=function(e,n){var o=this,s=t(e);if(!s.data("switcher")){if(this.options=t.extend({},i.defaults,n),this.element=s.on("click",this.options.toggler,function(t){t.preventDefault(),o.show(this)}),this.options.connect){this.connect=t(this.options.connect).find(".uk-active").removeClass(".uk-active").end();var a=this.element.find(this.options.toggler),r=a.filter(".uk-active");r.length?this.show(r):(r=a.eq(0),r.length&&this.show(r))}this.element.data("switcher",this)}};t.extend(i.prototype,{show:function(e){e=isNaN(e)?t(e):this.element.find(this.options.toggler).eq(e);var i=e;if(!i.hasClass("uk-disabled")){if(this.element.find(this.options.toggler).filter(".uk-active").removeClass("uk-active"),i.addClass("uk-active"),this.options.connect&&this.connect.length){var n=this.element.find(this.options.toggler).index(i);this.connect.children().removeClass("uk-active").eq(n).addClass("uk-active")}this.element.trigger("uk.switcher.show",[i])}}}),i.defaults={connect:!1,toggler:">*"},e.switcher=i,t(document).on("uk-domready",function(){t("[data-uk-switcher]").each(function(){var n=t(this);n.data("switcher")||new i(n,e.Utils.options(n.attr("data-uk-switcher")))})})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=function(e,n){var o=this,s=t(e);if(!s.data("tab")){if(this.element=s,this.options=t.extend({},i.defaults,n),this.options.connect&&(this.connect=t(this.options.connect)),window.location.hash){var a=this.element.children().filter(window.location.hash);a.length&&this.element.children().removeClass("uk-active").filter(a).addClass("uk-active")}var r=t('<li class="uk-tab-responsive uk-active"><a href="javascript:void(0);"></a></li>'),l=r.find("a:first"),u=t('<div class="uk-dropdown uk-dropdown-small"><ul class="uk-nav uk-nav-dropdown"></ul><div>'),c=u.find("ul");l.html(this.element.find("li.uk-active:first").find("a").text()),this.element.hasClass("uk-tab-bottom")&&u.addClass("uk-dropdown-up"),this.element.hasClass("uk-tab-flip")&&u.addClass("uk-dropdown-flip"),this.element.find("a").each(function(e){var i=t(this).parent(),n=t('<li><a href="javascript:void(0);">'+i.text()+"</a></li>").on("click",function(){o.element.data("switcher").show(e)});t(this).parents(".uk-disabled:first").length||c.append(n)}),this.element.uk("switcher",{toggler:">li:not(.uk-tab-responsive)",connect:this.options.connect}),r.append(u).uk("dropdown",{mode:"click"}),this.element.append(r).data({dropdown:r.data("dropdown"),mobilecaption:l}).on("uk.switcher.show",function(t,e){r.addClass("uk-active"),l.html(e.find("a").text())}),this.element.data("tab",this)}};i.defaults={connect:!1},e.tab=i,t(document).on("uk-domready",function(){t("[data-uk-tab]").each(function(){var n=t(this);n.data("tab")||new i(n,e.Utils.options(n.attr("data-uk-tab")))})})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i={},n=function(e,o){var s=this,a=t(e);a.data("search")||(this.options=t.extend({},n.defaults,o),this.element=a,this.timer=null,this.value=null,this.input=this.element.find(".uk-search-field"),this.form=this.input.length?t(this.input.get(0).form):t(),this.input.attr("autocomplete","off"),this.input.on({keydown:function(t){if(s.form[s.input.val()?"addClass":"removeClass"](s.options.filledClass),t&&t.which&&!t.shiftKey)switch(t.which){case 13:s.done(s.selected),t.preventDefault();break;case 38:s.pick("prev"),t.preventDefault();break;case 40:s.pick("next"),t.preventDefault();break;case 27:case 9:s.hide();break;default:}},keyup:function(){s.trigger()},blur:function(t){setTimeout(function(){s.hide(t)},200)}}),this.form.find("button[type=reset]").bind("click",function(){s.form.removeClass("uk-open").removeClass("uk-loading").removeClass("uk-active"),s.value=null,s.input.focus()}),this.dropdown=t('<div class="uk-dropdown uk-dropdown-search"><ul class="uk-nav uk-nav-search"></ul></div>').appendTo(this.form).find(".uk-nav-search"),this.options.flipDropdown&&this.dropdown.parent().addClass("uk-dropdown-flip"),this.dropdown.on("mouseover",">li",function(){s.pick(t(this))}),this.renderer=new i[this.options.renderer](this),this.element.data("search",this))};t.extend(n.prototype,{request:function(e){var i=this;this.form.addClass(this.options.loadingClass),this.options.source?t.ajax(t.extend({url:this.options.source,type:this.options.method,dataType:"json",success:function(t){t=i.options.onLoadedResults.apply(this,[t]),i.form.removeClass(i.options.loadingClass),i.suggest(t)}},e)):this.form.removeClass(i.options.loadingClass)},pick:function(t){var e=!1;if("string"==typeof t||t.hasClass(this.options.skipClass)||(e=t),"next"==t||"prev"==t){var i=this.dropdown.children().filter(this.options.match);if(this.selected){var n=i.index(this.selected);e="next"==t?i.eq(i.length>n+1?n+1:0):i.eq(0>n-1?i.length-1:n-1)}else e=i["next"==t?"first":"last"]()}e&&e.length&&(this.selected=e,this.dropdown.children().removeClass(this.options.hoverClass),this.selected.addClass(this.options.hoverClass))},trigger:function(){var t=this,e=this.value,i={};return this.value=this.input.val(),this.value.length<this.options.minLength?this.hide():(this.value!=e&&(this.timer&&window.clearTimeout(this.timer),this.timer=window.setTimeout(function(){i[t.options.param]=t.value,t.request({data:i})},this.options.delay,this)),this)},done:function(t){this.renderer.done(t)},suggest:function(t){t&&(t===!1?this.hide():(this.selected=null,this.dropdown.empty(),this.renderer.suggest(t),this.show()))},show:function(){this.visible||(this.visible=!0,this.form.addClass("uk-open"))},hide:function(){this.visible&&(this.visible=!1,this.form.removeClass(this.options.loadingClass).removeClass("uk-open"))}}),n.addRenderer=function(t,e){i[t]=e},n.defaults={source:!1,param:"search",method:"post",minLength:3,delay:300,flipDropdown:!1,match:":not(.uk-skip)",skipClass:"uk-skip",loadingClass:"uk-loading",filledClass:"uk-active",listClass:"results",hoverClass:"uk-active",onLoadedResults:function(t){return t},renderer:"default"};var o=function(e){this.search=e,this.options=t.extend({},o.defaults,e.options)};t.extend(o.prototype,{done:function(t){return t?(t.hasClass(this.options.moreResultsClass)?this.search.form.submit():t.data("choice")&&(window.location=t.data("choice").url),this.search.hide(),void 0):(this.search.form.submit(),void 0)},suggest:function(e){var i=this,n={click:function(e){e.preventDefault(),i.done(t(this).parent())}};this.options.msgResultsHeader&&t("<li>").addClass(this.options.resultsHeaderClass+" "+this.options.skipClass).html(this.options.msgResultsHeader).appendTo(this.search.dropdown),e.results&&e.results.length>0?(t(e.results).each(function(){var e=t('<li><a href="#">'+this.title+"</a></li>").data("choice",this);this.text&&e.find("a").append("<div>"+this.text+"</div>"),i.search.dropdown.append(e)}),this.options.msgMoreResults&&(t("<li>").addClass("uk-nav-divider "+i.options.skipClass).appendTo(i.dropdown),t("<li>").addClass(i.options.moreResultsClass).html('<a href="#">'+i.options.msgMoreResults+"</a>").appendTo(i.search.dropdown).on(n)),i.search.dropdown.find("li>a").on(n)):this.options.msgNoResults&&t("<li>").addClass(this.options.noResultsClass+" "+this.options.skipClass).html("<a>"+this.options.msgNoResults+"</a>").appendTo(i.search.dropdown)}}),o.defaults={resultsHeaderClass:"uk-nav-header",moreResultsClass:"uk-search-moreresults",noResultsClass:"",msgResultsHeader:"Search Results",msgMoreResults:"More Results",msgNoResults:"No results found"},n.addRenderer("default",o),e.search=n,t(document).on("focus.search.uikit","[data-uk-search]",function(){var i=t(this);i.data("search")||new n(i,e.Utils.options(i.attr("data-uk-search")))})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=t(window),n=[],o=function(){for(var t=0;n.length>t;t++)e.support.requestAnimationFrame.apply(window,[n[t].check])},s=function(i,o){var a=t(i);if(!a.data("scrollspy")){this.options=t.extend({},s.defaults,o),this.element=t(i);var r,l,u,c=this,d=function(){var t=e.Utils.isInView(c.element,c.options);t&&!l&&(r&&clearTimeout(r),u||(c.element.addClass(c.options.initcls),c.offset=c.element.offset(),u=!0,c.element.trigger("uk-scrollspy-init")),r=setTimeout(function(){t&&c.element.addClass("uk-scrollspy-inview").addClass(c.options.cls).width()},c.options.delay),l=!0,c.element.trigger("uk.scrollspy.inview")),!t&&l&&c.options.repeat&&(c.element.removeClass("uk-scrollspy-inview").removeClass(c.options.cls),l=!1,c.element.trigger("uk.scrollspy.outview"))
};d(),this.element.data("scrollspy",this),this.check=d,n.push(this)}};s.defaults={cls:"uk-scrollspy-inview",initcls:"uk-scrollspy-init-inview",topoffset:0,leftoffset:0,repeat:!1,delay:0},e.scrollspy=s;var a=[],r=function(){for(var t=0;a.length>t;t++)e.support.requestAnimationFrame.apply(window,[a[t].check])},l=function(n,o){var s=t(n);if(!s.data("scrollspynav")){this.element=s,this.options=t.extend({},l.defaults,o);var r,u=[],c=this.element.find("a[href^='#']").each(function(){u.push(t(this).attr("href"))}),d=t(u.join(",")),h=this,f=function(){r=[];for(var t=0;d.length>t;t++)e.Utils.isInView(d.eq(t),h.options)&&r.push(d.eq(t));if(r.length){var n=i.scrollTop(),o=function(){for(var t=0;r.length>t;t++)if(r[t].offset().top>=n)return r[t]}();if(!o)return;h.options.closest?c.closest(h.options.closest).removeClass(h.options.cls).end().filter("a[href='#"+o.attr("id")+"']").closest(h.options.closest).addClass(h.options.cls):c.removeClass(h.options.cls).filter("a[href='#"+o.attr("id")+"']").addClass(h.options.cls)}};this.options.smoothscroll&&e.smoothScroll&&c.each(function(){new e.smoothScroll(this,h.options.smoothscroll)}),f(),this.element.data("scrollspynav",this),this.check=f,a.push(this)}};l.defaults={cls:"uk-active",closest:!1,topoffset:0,leftoffset:0,smoothscroll:!1},e.scrollspynav=l;var u=function(){o(),r()};i.on("scroll",u).on("resize orientationchange",e.Utils.debounce(u,50)),t(document).on("uk-domready",function(){t("[data-uk-scrollspy]").each(function(){var i=t(this);i.data("scrollspy")||new s(i,e.Utils.options(i.attr("data-uk-scrollspy")))}),t("[data-uk-scrollspy-nav]").each(function(){var i=t(this);i.data("scrollspynav")||new l(i,e.Utils.options(i.attr("data-uk-scrollspy-nav")))})})}(jQuery,jQuery.UIkit),function(t,e){"use strict";var i=function(e,n){var o=this,s=t(e);s.data("smoothScroll")||(this.options=t.extend({},i.defaults,n),this.element=s.on("click",function(){var e=t(this.hash).length?t(this.hash):t("body"),i=e.offset().top-o.options.offset,n=t(document).height(),s=t(window).height();return e.outerHeight(),i+s>n&&(i=n-s),t("html,body").stop().animate({scrollTop:i},o.options.duration,o.options.transition),!1}),this.element.data("smoothScroll",this))};i.defaults={duration:1e3,transition:"easeOutExpo",offset:0},e.smoothScroll=i,t.easing.easeOutExpo||(t.easing.easeOutExpo=function(t,e,i,n,o){return e==o?i+n:n*(-Math.pow(2,-10*e/o)+1)+i}),t(document).on("click.smooth-scroll.uikit","[data-uk-smooth-scroll]",function(){var n=t(this);n.data("smoothScroll")||(new i(n,e.Utils.options(n.attr("data-uk-smooth-scroll"))),n.trigger("click"))})}(jQuery,jQuery.UIkit),function(t,e,i){var n=function(t,i){var o=this,s=e(t);s.data("toggle")||(this.options=e.extend({},n.defaults,i),this.totoggle=this.options.target?e(this.options.target):[],this.element=s.on("click",function(t){t.preventDefault(),o.toggle()}),this.element.data("toggle",this))};e.extend(n.prototype,{toggle:function(){this.totoggle.length&&this.totoggle.toggleClass(this.options.cls)}}),n.defaults={target:!1,cls:"uk-hidden"},i.toggle=n,e(document).on("click.toggle.uikit","[data-uk-toggle]",function(){var t=e(this);t.data("toggle")||(new n(t,i.Utils.options(t.attr("data-uk-toggle"))),t.trigger("click"))})}(this,jQuery,jQuery.UIkit);

