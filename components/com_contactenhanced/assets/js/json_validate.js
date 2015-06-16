function jsonvalidate(cf_id,url){
	var cf	= document.id('cf_'+cf_id);
	var log	= document.id('cf_'+cf_id+'-ajax-response');
	cf.addEvent('blur', function(){
		var urlScript	= 'index.php?option=com_contactenhanced&task=jsonExecuteCF&cf='+cf_id;
		log.addClass('cf_'+cf_id+'ajax-loading');
		log.setStyle('display', 'block');
		var jSonRequest = new Request.JSON({url:urlScript, onSuccess: function(response){
				if(response.action == 'success'){
					cf.removeClass('invalid');
					cf.addClass('success');
					cf.set('aria-invalid', 'false');
					log.setStyle('display', 'none');
		    	}else{
		    		cf.removeClass('success');
		    		cf.addClass('invalid');
		    		cf.set('aria-invalid', 'true');	
		    		log.addClass('validation-advice');
		    	}
				log.set('html',response.msg);
		    }
		}).get(({'q':cf.get('value')})); 
	});
	
}