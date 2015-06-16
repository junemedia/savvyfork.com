//window.loadFirebugConsole(); 
var JsonSelect = ({
	updateSelect: function(sisterFieldId,el,url)
	{
		urlScript	= url+'index.php?option=com_contactenhanced&task=getChainSelect&tmpl=raw';
		if((document.id(sisterFieldId) != undefined) && (document.id(sisterFieldId+'value') != undefined)){
			alert('Field ID '+sisterFieldId+' is not defined');
			return false; //aborts
		}
		var sisEl	= document.id(sisterFieldId+'-container');
		sisterDiv	= sisEl.getParent();
		sisterDiv.addClass('loading-chainselect');
		var jSonRequest = new Request.JSON({url:urlScript, onSuccess: function(response){	
			//did it return as good, or bad?
			if(response.action == 'success'){
				JsonSelect.loadData(response.value,sisterFieldId+'value');
			}
			sisterDiv.removeClass('loading-chainselect');
		}
		}).get(({'selectedOption':el.value,'fieldID':sisterFieldId}));		
	},
	// Creates select options
	createOpt: function(strVal,strText,objTargetSelect) {
		var opt = new Element('option');
		opt.setProperty('value',strVal);
		opt.set('text',strText);
		opt.injectInside(objTargetSelect);
	},
	// Loads in the data to the select based on the previous selection
	loadData: function(objData, targetSelect) {
		// Removes the children of the target element
		var objTargetSelect = document.id(targetSelect);
		objTargetSelect.empty();
		objData.each(function(item, index){
			JsonSelect.createOpt(item.value,item.text,targetSelect);
		});
	}
});

