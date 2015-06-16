var JsonSelect = ({
	updateSelect: function(sisterFieldId,el,url)
	{
		urlScript	= url+'index.php?option=com_contactenhanced&task=getChainSelect&tmpl=raw';
		if((jQuery('#'+sisterFieldId+'value') == undefined)){
			alert("Field ID '"+'#'+sisterFieldId+'value'+"' is not defined");
			return false; //aborts
		}
		var sisEl	= jQuery('#'+sisterFieldId+'-container');
		sisterDiv	= sisEl.parent();
		sisterDiv.addClass('loading-chainselect');
		jQuery.getJSON(urlScript, {'selectedOption':el.value,'fieldID':sisterFieldId}, function(data) {
			//did it return as good, or bad?
			if(data.action == 'success'){
				JsonSelect.loadData(data.value,sisterFieldId+'value');
			}
			sisterDiv.removeClass('loading-chainselect');
		});
			
	},
	// Loads in the data to the select based on the previous selection
	loadData: function(objData, targetSelect) {
		// Removes the children of the target element
		var objTargetSelect = jQuery('#'+targetSelect);
		objTargetSelect.empty();
		objTargetSelect.html(objData);
	}
});

