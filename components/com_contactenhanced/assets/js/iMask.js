var $chk=function(a){return!!(a||a===0)};var iMask=new Class({options:{targetClass:".iMask",maskEmptyChr:' ',validNumbers:"1234567890",validAlphas:"abcdefghijklmnopqrstuvwxyz",validAlphaNums:"abcdefghijklmnopqrstuvwxyz1234567890",onFocus:function(){},onBlur:function(){},onValid:function(){},onInvalid:function(){},onKeyDown:function(){}},initialize:function(c){this.setOptions(c);var d=$$(this.options.targetClass);d.each(function(b,i){b.options=JSON.decode(b.alt);if(b.options.type=="number"){b.setStyle("text-align","right")}b.addEvent("mousedown",function(a){a.stop()});b.addEvent("mouseup",function(a){a.stop();this._onMouseUp(a,b)}.bind(this));b.addEvent("click",function(a){a.stop()});b.addEvent("keydown",function(a){this._onKeyDown(a,b);this.fireEvent("onKeyDown",b,20)}.bind(this));b.addEvent("keypress",function(a){this._onKeyPress(a,b)}.bind(this));b.addEvent("focus",function(a){a.stop();this._onFocus(a,b);this.fireEvent("onFocus",b,20)}.bind(this));b.addEvent("blur",function(a){a.stop();this._onBlur(a,b);this.fireEvent("onBlur",b,20)}.bind(this))}.bind(this))},_onMouseUp:function(a,b){if(b.options.type=="fixed"){var p=this._getSelectionStart(b);this._setSelection(b,p,(p+1))}else if(b.options.type=="number"){this._setEnd(b)}},_onKeyDown:function(a,b){if(a.code==13){b.blur();this._submitForm(b)}else if(!(a.code==9)){a.stop();if(b.options.type=="fixed"){var p=this._getSelectionStart(b);switch(a.code){case 8:this._updSelection(b,p,this.options.maskEmptyChr);this._selectPrevious(b);break;case 36:this._selectFirst(b);break;case 35:this._selectLast(b);break;case 37:case 38:this._selectPrevious(b);break;case 39:case 40:this._selectNext(b);break;case 46:this._updSelection(b,p,this.options.maskEmptyChr);this._selectNext(b);break;default:var c=this._chrFromEvent(a);if(this._isViableInput(b,p,c)){if(a.shift){this._updSelection(b,p,c.toUpperCase())}else{this._updSelection(b,p,c)}this.fireEvent("onValid",[a,b],20);this._selectNext(b)}else{this.fireEvent("onInvalid",[a,b],20)}break}}else if(b.options.type=="number"){switch(a.code){case 8:case 46:this._popNumber(b);break;default:var c=this._chrFromEvent(a);if(this.options.validNumbers.indexOf(c)>=0){this._pushNumber(b,c);this.fireEvent("onValid",[a,b],20)}else{this.fireEvent("onInvalid",[a,b],20)}break}}}},_onKeyPress:function(a,b){if(!(a.code==9)&&!(a.shift&&a.code==9)&&!(a.code==13)&&!(a.ctrl&&a.code==67)&&!(a.ctrl&&a.code==86)&&!(a.ctrl&&a.code==88)){a.stop()}},_onFocus:function(a,b){b.value=this._wearMask(b,b.value);if(b.options.type=="fixed"){this._selectFirst.delay(20,this,b)}else{this._setEnd.delay(20,this,b)}},_onBlur:function(a,b){if(b.options.stripMask)b.value=this._stripMask(b)},_selectAll:function(a){this._setSelection(a,0,a.value.length)},_selectFirst:function(a){for(var i=0,len=a.options.mask.length;i<len;i++){if(this._isInputPosition(a,i)){this._setSelection(a,i,(i+1));return}}},_selectLast:function(a){for(var i=(a.options.mask.length-1);i>=0;i--){if(this._isInputPosition(a,i)){this._setSelection(a,i,(i+1));return}}},_selectPrevious:function(a,p){if(!$chk(p))p=this._getSelectionStart(a);if(p<=0){this._selectFirst(a)}else{if(this._isInputPosition(a,(p-1))){this._setSelection(a,(p-1),p)}else{this._selectPrevious(a,(p-1))}}},_selectNext:function(a,p){if(!$chk(p))p=this._getSelectionEnd(a);if(p>=a.options.mask.length){this._selectLast(a)}else{if(this._isInputPosition(a,p)){this._setSelection(a,p,(p+1))}else{this._selectNext(a,(p+1))}}},_setSelection:function(c,a,b){if(c.setSelectionRange){c.focus();c.setSelectionRange(a,b)}else if(c.createTextRange){var r=c.createTextRange();r.collapse();r.moveStart("character",a);r.moveEnd("character",(b-a));r.select()}},_updSelection:function(a,p,b){var c=a.value;var d="";d+=c.substring(0,p);d+=b;d+=c.substr(p+1);a.value=d;this._setSelection(a,p,(p+1))},_setEnd:function(a){var b=a.value.length;this._setSelection(a,b,b)},_getSelectionStart:function(a){var p=0;if(a.selectionStart){if(typeOf(a.selectionStart)=="number")p=a.selectionStart}else if(document.selection){var r=document.selection.createRange().duplicate();r.moveEnd("character",a.value.length);p=a.value.lastIndexOf(r.text);if(r.text=="")p=a.value.length}return p},_getSelectionEnd:function(a){var p=0;if(a.selectionEnd){if(typeOf(a.selectionEnd)=="number"){p=a.selectionEnd}}else if(document.selection){var r=document.selection.createRange().duplicate();r.moveStart("character",-a.value.length);p=r.text.length}return p},_isInputPosition:function(a,p){var b=a.options.mask.toLowerCase();var c=b.charAt(p);if("9ax".indexOf(c)>=0)return true;return false},_isViableInput:function(a,p,b){var c=a.options.mask.toLowerCase();var d=c.charAt(p);switch(d){case'9':if(this.options.validNumbers.indexOf(b)>=0)return true;break;case'a':if(this.options.validAlphas.indexOf(b)>=0)return true;break;case'x':if(this.options.validAlphaNums.indexOf(b)>=0)return true;break;default:return false;break}},_wearMask:function(a,b){var c=a.options.mask.toLowerCase();var d="";for(var i=0,u=0,len=c.length;i<len;i++){switch(c.charAt(i)){case'9':if(this.options.validNumbers.indexOf(b.charAt(u).toLowerCase())>=0){if(b.charAt(u)==""){d+=this.options.maskEmptyChr}else{d+=b.charAt(u++)}}else{d+=this.options.maskEmptyChr}break;case'a':if(this.options.validAlphas.indexOf(b.charAt(u).toLowerCase())>=0){if(b.charAt(u)==""){d+=this.options.maskEmptyChr}else{d+=b.charAt(u++)}}else{d+=this.options.maskEmptyChr}break;case'x':if(this.options.validAlphaNums.indexOf(b.charAt(u).toLowerCase())>=0){if(b.charAt(u)==""){d+=this.options.maskEmptyChr}else{d+=b.charAt(u++)}}else{d+=this.options.maskEmptyChr}break;default:d+=c.charAt(i);if(b.charAt(u)==c.charAt(i)){u++}break}}return d},_stripMask:function(a){var b=a.value;if(""==b)return"";var c="";if(a.options.type=="fixed"){for(var i=0,len=b.length;i<len;i++){if((b.charAt(i)!=this.options.maskEmptyChr)&&(this._isInputPosition(a,i))){c+=b.charAt(i)}}}else if(a.options.type=="number"){for(var i=0,len=b.length;i<len;i++){if(this.options.validNumbers.indexOf(b.charAt(i))>=0){c+=b.charAt(i)}}}return c},_chrFromEvent:function(a){var b='';switch(a.code){case 48:case 96:b='0';break;case 49:case 97:b='1';break;case 50:case 98:b='2';break;case 51:case 99:b='3';break;case 52:case 100:b='4';break;case 53:case 101:b='5';break;case 54:case 102:b='6';break;case 55:case 103:b='7';break;case 56:case 104:b='8';break;case 57:case 105:b='9';break;default:b=a.key;break}return b},_pushNumber:function(a,b){a.value=a.value+b;this._formatNumber(a)},_popNumber:function(a){a.value=a.value.substring(0,(a.value.length-1));this._formatNumber(a)},_formatNumber:function(a){var b=this._stripMask(a);var c="";for(var i=0,d=b.length;i<d;i++){if('0'!=b.charAt(i)){c=b.substr(i);break}}b=c;c="";for(var d=b.length,i=a.options.decDigits;d<=i;d++){c+="0"}c+=b;b=c.substr(c.length-a.options.decDigits);c=c.substring(0,(c.length-a.options.decDigits));var e=new RegExp("(\\d+)(\\d{"+a.options.groupDigits+"})");while(e.test(c)){c=c.replace(e,"$1"+a.options.groupSymbol+"$2")}a.value=c+a.options.decSymbol+b},_getObjForm:function(a){var b=a.getParent();if(b.getTag()=="form"){return b}else{return this._getObjForm(b)}},_submitForm:function(a){var b=this._getObjForm(a);b.submit()}});iMask.implement(new Events);iMask.implement(new Options);window.addEvent('domready',function(){new iMask({onFocus:function(a){a.setStyles({"background-color":"#ff8"})},onBlur:function(a){a.setStyles({"background-color":"#fff"})},onValid:function(a,b){b.setStyles({"background-color":"#8f8"})},onInvalid:function(a,b){if(!a.shift){b.setStyles({"background-color":"#f88"})}}})});