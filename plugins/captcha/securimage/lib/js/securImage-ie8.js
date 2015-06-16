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

function validateSICaptcha(captcha){
	var urlScript	= icaptchaURI+'plugins/captcha/securimage/lib/verify_json.php';
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
	if(icaptchaUseAjax.get('value') == 1){	
		Array.each($$('input.sicaptcha'), function(captcha, index){
			captcha.addEvent('blur',function(){
				validateSICaptcha(captcha);
			});
			captcha.addEvent('keydown',function(event){
				if(event.key == 'enter'){validateSICaptcha(captcha);}
			});
		});
	}
});