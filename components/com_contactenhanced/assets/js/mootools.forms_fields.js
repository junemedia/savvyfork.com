window.addEvent('domready', function(){
	Form.Validator.add('validate-boxes', {
		errorMsg: Form.Validator.getMsg.pass('oneRequired'),
		test: function(element, props){
			var p = document.id(props['validate-boxes']) || element.getParent(props['validate-boxes']);
			p = p.getParent(); // get grandparent
			return p.getElements('input').some(function(el){
				if (['checkbox', 'radio'].contains(el.get('type'))) return el.get('checked');
				return el.get('value');
			});
		}
	});
	Form.Validator.add('validate-limited-textarea', {
		errorMsg: Joomla.JText._('COM_CONTACTENHANCED_CF_MULTITEXT_CHARACTER_LIMIT_REACHED'),
		test: function(element, props){
			
			var maxlen	= document.id((element.id+'-maxlen'));
			maxlen	= maxlen.get('value');
			if(maxlen > 0 && element.value.length >= maxlen){
				return false;
			}else{
				return true;
			}
		}
	});
	Form.Validator.add('validate-passverify', {
		errorMsg: Joomla.JText._('COM_CONTACTENHANCED_CF_PASSWORD_VERIFY_VALIDATION_ERROR_MSG'),
		test: function(element){
		  if(element.value.length < 2)  return false;
		  var password  = document.id(('password'));
		  if(password.get('value') != element.get('value')){
			return false;
		  }else{
			return true;
		  }
		}
	  });
});