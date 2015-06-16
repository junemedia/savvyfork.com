function updateCaptchas(){
	var captchas = document.getElementsByName('captcha'); 
	var captchasFields = $$('input.sicaptcha');
	var imgURL	=icaptchaURI+'plugins/captcha/securimage/lib/show.php?'
					+'random=' +Math.random()+'.'+Math.random();
	for (var i=0; i<captchas.length; i++){
		captchas[i].src	= imgURL; //+'&securimage_namespace='+captchas[i].id;
		captchasFields[i].set('value','');
	}
}
function updateCaptcha(namespace){
	var captcha = document.id('img-'+namespace); 
	captcha.src	= icaptchaURI+'plugins/captcha/securimage/lib/show.php?'
					+'&securimage_namespace='+namespace
					+'&random=' +Math.random()+'.'+Math.random();
}
function initCaptcha(){
	var captchas = document.getElementsByName('captcha'); 
	var captchasFields = $$('input.sicaptcha');
	var imgURL	=icaptchaURI+'plugins/captcha/securimage/lib/show.php?'
					+'&random=' +Math.random()+'.'+Math.random();
	for (var i=0; i<captchas.length; i++){
		captchas[i].src	= imgURL+'&securimage_namespace='+captchas[i].id;
		captchasFields[i].set('value','');
	}
}
function validateSICaptcha(captcha){
	var urlScript	= icaptchaURI+'plugins/captcha/securimage/lib/verify_json.php?securimage_namespace='+captcha.get('id');
	var jSonRequest = new Request.JSON({url:urlScript, onSuccess: function(response){
			icaptchaValidator	= document.id((captcha.get('id')+'-validation'));
			if(response.action == 'success'){
	    		captcha.removeClass('invalid');
	    		captcha.addClass('success');
	    		captcha.set('aria-invalid', 'false');
	    		icaptchaValidator.set('value',1);
	    		parentForm	= captcha.getParent('form');
	    		if(parentForm){
	    			var parentFormValidator = new Form.Validator.Inline(parentForm);
	    			parentFormValidator.validateField(captcha);  
	    		}
	    	}else{
	    		icaptchaValid = false;
	    		captcha.removeClass('success');
	    		captcha.addClass('invalid');
	    		captcha.set('aria-invalid', 'true');
	    		icaptchaValidator.set('value',0); 		
	    	}
	    }
		}).get(({'captcha_code':captcha.get('value')})); 
}

window.addEvent('domready', function(){
	var icaptchaUseAjax = document.id(('icaptchaUseAjax'));
	if(icaptchaUseAjax  && icaptchaUseAjax.get('value') == 1){	
		Array.each($$('input.sicaptcha'), function(captcha, index){
			captcha.addEvent('blur',function(){
				validateSICaptcha(captcha);
			});
			captcha.addEvent('keydown',function(e){
				if(e.key == 'enter'){validateSICaptcha(captcha);e.stop();}
			});
		});
		
		// Does not work correctly with IE8 and older, so I had to create a js file for those browsers 
		Form.Validator.add('sicaptcha', {
			errorMsg: Joomla.JText._('PLG_CAPTCHA_SECURIMAGE_VALIDATION_CODE_WRONG'),
			test: function(element){
				if(element.value.length < 1)	return false;
				icaptchaValidator	= document.id((element.id+'-validation'));
				if(icaptchaValidator.get('value') == 0){
					validateSICaptcha(element); // required for IE9
					return false;
				}else{
					return true;
				}
			}
		});
	}
});