formParamHelper = new Class( { 
	initialize : function( control, options ){
		this.groups = [];	
		var _default = '';
		var groups = this.getGroup( control );
		this._control = control;
	//	console.log('groups');
	//	console.log(groups);
		if( (groups != undefined) ) {
			groups._parent	= this;
			groups.addEvent('change', function() {
				groups._parent.update(groups.value);
			}.bind(this));
			if(jQuery != undefined){
				jQuery(groups).change(function() {
					groups._parent.update(groups.value);
					console.log(groups.value);
				});
			}
		}
		console.log(groups);
		this.update(this._default);		
	},
	
	update: function(_default){
		if(!this.items) return false;
		
		this.items.each(function(item, index){
			var itemClass	= item.get('class');
			var itemParent	= item.getParent('.control-group');
			//console.log(itemParent);
			if( (item.tagName.toLowerCase() == 'div' && itemClass.test('cf-switcher','i'))
					|| (itemClass != undefined && itemClass.test("chzn",'i'))
					|| item.id.test("chzn",'i')
					|| item == undefined
			){
				return;
			}
			if ((item.id && item.id.test(this._control+'_'+_default ))){
				display = '';
				disabled = false;
			}else{
				display = 'none';
				disabled = true;	
			}
			if( itemParent != undefined ){
				itemParent.setStyles( {"display":display} );
			}
		}.bind(this) );
		//this.updateHeight ();
	},
	updateHeight: function () {
		if (this._fieldParent && this._container) 
			this._container.setStyle('height', this._fieldParent.offsetHeight);
	},
	getGroup: function (control) {
		var frm = document.forms['adminForm'];
		var obj = frm['jform[params]['+control+']'];
				if (!obj) return null;
		var objs;
		if (obj.tagName == 'SELECT') {
			objs = $(obj).getElements('option');
		} else {
			if (obj.length < 1) return null;
			objs = obj = $$(obj);
		}
		
		this._container = this.getParentByTagName(obj, 'div');
		this._container = this.getParentByTagName(this._container, 'div');
		
		this._fieldParent = this.getParentByTagName(obj, 'fieldset');
		
		objs.each (function(group){
			this.groups.push(group.value);
			if( group.selected || group.checked){ 
				this._default = group.value;
			}
		}.bind(this));
		
		this.items = $( document.body ).getElements("*[id^=jform_params_"+control+"_"+"]");
		return obj;
	},
	
	getParentByTagName: function (el, tag) {
		var parent = $(el).getParent();
		while (!parent || (parent.tagName.toLowerCase() != tag.toLowerCase() )) {
			parent = parent.getParent();
		}
		return parent;
	}
});

var paramhelpergroups = new Array();

function initparamhelpergroup(control, options) {
	paramhelpergroups.push (new formParamHelper(control, options));
}

