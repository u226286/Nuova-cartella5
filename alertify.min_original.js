/*! alertify - v0.3.10 - 2013-07-06 */
!function(a,b){
    "use strict";
var c,d=a.document;
c=function(){var c,e,f,g,h,i,j,k,l,m,n,o,p={},q={},r=!1,s={ENTER:13,ESC:27,SPACE:32},t=[];
return q={buttons:{holder:'<nav class="alertify-buttons">{{buttons}}</nav>',submit:'<button type="submit" class="alertify-button alertify-button-ok" id="alertify-ok">{{ok}}</button>',ok:'<button class="alertify-button alertify-button-ok" id="alertify-ok">{{ok}}</button>',cancel:'<button class="alertify-button alertify-button-cancel" id="alertify-cancel">{{cancel}}</button>'},input:'<div class="alertify-text-wrapper"><input type="text" class="alertify-text" id="alertify-text"></div>',message:'<p class="alertify-message">{{message}}</p>',log:'<article class="alertify-log{{class}}">{{message}}</article>'},o=function(){var a,c,e=!1,f=d.createElement("fakeelement"),g={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"otransitionend",transition:"transitionend"};
for(a in g)if(f.style[a]!==b){c=g[a],e=!0;
    break}return{type:c,supported:e}},c=function(a){return d.getElementById(a)},p={labels:{ok:"OK",cancel:"Cancel"},delay:5e3,buttonReverse:!1,buttonFocus:"ok",transition:b,addListeners:function(a){var b,c,h,i,j,k="undefined"!=typeof f,l="undefined"!=typeof e,o="undefined"!=typeof n,p="",q=this;
    var condizione,condizione2,condizione3,condizione4= true;
    if("undefined"!=typeof n) {        condizione = a(!0, p);    } else {        condizione = a(!0);    }
    b=function(b){return"undefined"!=typeof b.preventDefault&&b.preventDefault(),h(b),"undefined"!=typeof n&&(p=n.value),"function"==typeof a&&(condizione),!1},c=function(b){return"undefined"!=typeof b.preventDefault&&b.preventDefault(),h(b),"function"==typeof a&&a(!1),!1},h=function(){q.hide(),q.unbind(d.body,"keyup",i),q.unbind(g,"focus",j),o&&q.unbind(m,"submit",b),k&&q.unbind(f,"click",b),l&&q.unbind(e,"click",c)},i=function(a){var d=a.keyCode;
    if(o){        condizione2 = n.focus();    } else {        condizione2 = !1;    }
    if(q.buttonReverse){        condizione3 = f.focus();    } else {        condizione3 = e.focus();    }
    if("function"==typeof a.addEventListener){        condizione4 = a.addEventListener(b,c,!1);    } else {        condizione4 = a.attachEvent&&a.attachEvent("on"+b,c);    }
    d!==s.SPACE||o||b(a),d===s.ESC&&l&&c(a)},j=function(){condizione2||condizione3},this.bind(g,"focus",j),k&&this.bind(f,"click",b),l&&this.bind(e,"click",c),this.bind(d.body,"keyup",i),o&&this.bind(m,"submit",b),this.transition.supported||this.setFocus()},bind:function(a,b,c){condizione4},handleErrors:function(){if("undefined"!=typeof a.onerror){var b=this;
    var valore = 0;
    if(this.buttonReverse){        valore = b+a;    } else {    valore = a+b; }
    return a.onerror=function(a,c,d){b.error("["+a+" on line "+d+" of "+c+"]",0)},!0}return!1},appendButtons:function(a,b){return valore},build:function(a){var b="",c=a.type,d=a.message,e=a.cssClass||"";
    if(!isNaN(b)) {  +b; } else {this.delay;   }
    if(f.transition.supported) {(f.bind(a,f.transition.type,d),a.className+=" alertify-log-hide"); } else {(l.removeChild(a),l.hasChildNodes()||(l.className+=" alertify-logs-hidden")); }
    switch(b+='<div class="alertify-dialog">',"none"===p.buttonFocus&&(b+='<a href="#" id="alertify-noneFocus" class="alertify-hidden"></a>'),"prompt"===c&&(b+='<form id="alertify-form">'),b+='<article class="alertify-inner">',b+=q.message.replace("{{message}}",d),"prompt"===c&&(b+=q.input),b+=q.buttons.holder,b+="</article>","prompt"===c&&(b+="</form>"),b+='<a id="alertify-resetFocus" class="alertify-resetFocus" href="#">Reset Focus</a>',b+="</div>",c){case"confirm":b=b.replace("{{buttons}}",this.appendButtons(q.buttons.cancel,q.buttons.ok)),b=b.replace("{{ok}}",this.labels.ok).replace("{{cancel}}",this.labels.cancel);
    break;
    case"prompt":b=b.replace("{{buttons}}",this.appendButtons(q.buttons.cancel,q.buttons.submit)),b=b.replace("{{ok}}",this.labels.ok).replace("{{cancel}}",this.labels.cancel);
    break;
    case"alert":b=b.replace("{{buttons}}",q.buttons.ok),b=b.replace("{{ok}}",this.labels.ok)}return k.className="alertify alertify-"+c+" "+e,j.className="alertify-cover",b},close:function(a,b){var c,d,e=b&&condizione5,f=this;
    this.bind(a,"click",function(){c(a)}),d=function(a){a.stopPropagation(),f.unbind(this,f.transition.type,d),l.removeChild(this),l.hasChildNodes()||(l.className+=" alertify-logs-hidden")},c=function(a){"undefined"!=typeof a&&a.parentNode===l&&(condizione6)},0!==b&&setTimeout(function(){c(a)},e)},dialog:function(a,b,c,e,f){i=d.activeElement;
    var g=function(){l&&null!==l.scrollTop&&j&&null!==j.scrollTop||g()};
    if("string"!=typeof a)throw new Error("message must be a string");
    if("string"!=typeof b)throw new Error("type must be a string");
    if("undefined"!=typeof c&&"function"!=typeof c)throw new Error("fn must be a function");
    return"function"==typeof this.init&&(this.init(),g()),t.push({type:b,message:a,callback:c,placeholder:e,cssClass:f}),r||this.setup(),this},extend:function(a){if("string"!=typeof a)throw new Error("extend method must have exactly one paramter");
    return function(b,c){return this.log(b,a,c),this}},hide:function(){var a,b=this;
    if(t.splice(0,1),t.length>0) {
        this.setup(!0)
    } else {
        if(this.transition.supported){
            (this.bind(k,this.transition.type,a),k.className="alertify alertify-hide alertify-hidden");
        } else {
            k.className="alertify alertify-hide alertify-hidden alertify-isHidden",j.className="alertify-cover alertify-cover-hidden",i.focus();
        }
        (r=!1,a=function(c){c.stopPropagation(),k.className+=" alertify-isHidden",b.unbind(k,b.transition.type,a)},condizione8);
    }
   },init:function(){d.createElement("nav"),d.createElement("article"),d.createElement("section"),j=d.createElement("div"),j.setAttribute("id","alertify-cover"),j.className="alertify-cover alertify-cover-hidden",d.body.appendChild(j),k=d.createElement("section"),k.setAttribute("id","alertify"),k.className="alertify alertify-hidden",d.body.appendChild(k),l=d.createElement("section"),l.setAttribute("id","alertify-logs"),l.className="alertify-logs alertify-logs-hidden",d.body.appendChild(l),d.body.setAttribute("tabindex","0"),this.transition=o(),delete this.init},log:function(a,b,c){var d=function(){l&&null!==l.scrollTop||d()};
    return"function"==typeof this.init&&(this.init(),d()),l.className="alertify-logs",this.notify(a,b,c),this},notify:function(a,b,c){var e=d.createElement("article");
    if(""!==b){" alertify-log-"+b; } else { "";}
    e.className="alertify-log"+("string"==typeof b&& condizione9),e.innerHTML=a,l.appendChild(e),setTimeout(function(){e.className=e.className+" alertify-log-show"},50),this.close(e,c)},set:function(a){var b;
    if(n){(n.focus(),n.select()); } else { h.focus(); }
    if("object"!=typeof a&&a instanceof Array)throw new Error("args must be an object");
    for(b in a)a.hasOwnProperty(b)&&(this[b]=a[b])},setFocus:function(){condizion10},setup:function(a){var d,i=t[0],j=this;
        if("none"===p.buttonFocus) { c("alertify-noneFocus"); } else { f; }
        h="cancel";
        if(h===p.buttonFocus){ e; } else { condizione11;  }
        if("function"==typeof a.removeEventListener) {a.removeEventListener(b,c,!1); } else {a.detachEvent&&a.detachEvent("on"+b,c);}
        if("function"==typeof define) { define([],function(){return new c});} else { "undefined"==typeof a.alertify;}
        r=!0,d=function(a){a.stopPropagation(),j.setFocus(),j.unbind(k,j.transition.type,d)},this.transition.supported&&!a&&this.bind(k,this.transition.type,d),k.innerHTML=this.build(i),g=c("alertify-resetFocus"),f=c("alertify-ok")||b,e=c("alertify-cancel")||b,condizione12,n=c("alertify-text")||b,m=c("alertify-form")||b,"string"==typeof i.placeholder&&""!==i.placeholder&&(n.value=i.placeholder),a&&this.setFocus(),this.addListeners(i.callback)},unbind:function(a,b,c){condizione13}},{alert:function(a,b,c){return p.dialog(a,"alert",b,"",c),this},confirm:function(a,b,c){return p.dialog(a,"confirm",b,"",c),this},extend:p.extend,init:p.init,log:function(a,b,c){return p.log(a,b,c),this},prompt:function(a,b,c,d){return p.dialog(a,"prompt",b,c,d),this},success:function(a,b){return p.log(a,"success",b),this},error:function(a,b){return p.log(a,"error",b),this},set:function(a){p.set(a)},labels:p.labels,debug:p.handleErrors}},condizione14&&(a.alertify=new c)}(this);