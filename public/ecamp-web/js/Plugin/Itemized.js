var Ecamp = Ecamp || {};
Ecamp.Web = Ecamp.Web || {};
Ecamp.Web.Plugin = Ecamp.Web.Plugin || {};

Ecamp.Web.Plugin.Itemized = function (config) {
	
	if (!(this instanceof Ecamp.Web.Plugin.Itemized)) {
        return new Ecamp.Web.Plugin.Itemized();
    }
    
	// store 'this' for usage within callbacks
    var self = this;
	
    // call parent constructor
	Ecamp.Web.Plugin.call(this, config);
	
	this.config.showNewForm  = config.showNewForm  !== false; // default is true, for any values except explicit "false"
	this.config.movableItems = config.movableItems !== false; // default is true, for any values except explicit "false"

	this.containerElm = this.eventPluginElm.find(".items");
	
	if(this.config.showNewForm){
		/* Initialize create new item form */
		this.eventPluginElm.find('form.createItem').find('.submit').click( function(){
			form = self.eventPluginElm.find('form.createItem');
			
			$.ajax({
	            type: form.attr('method'),
	            url: form.attr('action'),
	            data: form.serialize(),
	            global: false,
	            
	            statusCode: {
					200: function(data, statusText, request){
						var item = $(data);
						self.containerElm.append(item);
						self.initItem(item);
						form[0].reset();
					},

				    500: function(data, status){
				    	console.error(data);
					}
				}
	        });
			
			return false;
        });
	} else {
		/* Initialize create new item button */
		this.eventPluginElm.find('.createItem').click( function(){
			$.get($(this).attr('href'))
			.done(function(data){
				var item = $(data);
				self.containerElm.append(item);
				self.initItem(item);
			})
			.fail(function(data){ console.error(data);});
			
			return false;
		});
	}
	
	/* initalize all items */
	this.containerElm
	.find('.item')
	.each(function(idx, elm){
		self.initItem(elm);
	});
};

Ecamp.Web.Plugin.Itemized.prototype = Object.create(Ecamp.Web.Plugin.prototype);

Ecamp.Web.Plugin.Itemized.prototype.initItem = function(itemElm){
	
	// store 'this' for usage within callbacks
	var self = this;
	
	var itemElm = $(itemElm);
	var showElm = itemElm.find('div[name=show]');
	var editElm = itemElm.find('div[name=edit]');
	var origData = null;

	showElm.hide();
	editElm.hide();

	var editFunc = function(){
		showElm.hide();

		origData = {};
		editElm.find('.form-element').each(function(idx, elm){
			origData[$(elm).attr('id')] = $(elm).val();
		});

		editElm.show();

		editElm.find('textarea.autosize').trigger('autosize.resize');
		editElm.find('.form-element:first').focus();
		
		return false;
	};

	var saveFunc = function(){
		var form = editElm.find('form');

		$.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),

            // prevents global error handling, errors handled locally
            global: false,

            statusCode: {
				200: function(data, statusText, request){
					var respElm = $(data);
					//respElm.find('.dropdown-toggle').dropdown();
					itemElm.replaceWith(respElm);
					self.initItem(respElm);

					origData = null;
				},

			    500: function(data, status){
			    	console.error(data);
				}
			}
        });
		
		return false;
	};

	var cancelFunc = function(){
		editElm.hide();

		editElm.find('.form-element').each(function(idx, elm){
			$(elm).val(origData[$(elm).attr('id')]);
		});
		//editElm.find('select.selectpicker').selectpicker('refresh');
		origData = null;

		showElm.show();
		
		return false;
	};

	var deleteFunc = function(e){
		$.get($(this).attr('href'))
		 .then(function(){
			 $(itemElm).remove();
		  });
		
		return false;
	};

	var moveUpFunc = function(e){
		$.get($(this).attr('href'))
		 .then(function(){
			$(itemElm).after($(itemElm).prev());
		  });
		
		return false;
	};

	var moveDownFunc = function(e){
		$.get($(this).attr('href'))
		 .then(function(){
			 $(itemElm).before($(itemElm).next());
		  });
		
		return false;
	};
	
	itemElm.find('.deleteItem').click(deleteFunc);
	itemElm.find('.editItem').click(editFunc);
	itemElm.find('.saveItem').click(saveFunc);
	itemElm.find('.cancelItem').click(cancelFunc);
	
	if( this.config.movableItems ){
		itemElm.find('.moveUpItem').click(moveUpFunc);
		itemElm.find('.moveDownItem').click(moveDownFunc);
	}
	
	showElm.show();
	
	itemElm.find('textarea.autosize').autosize();
};