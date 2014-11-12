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
		
		var addFunc =  function(){
			form = self.eventPluginElm.find('[name=add]').find('form');
			
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
						
						form = self.eventPluginElm.find('[name=add]').find('form');
						form[0].reset();
					},
					
					/* validation error */
					422: function(data, status){
						var respElm = $(data.responseText);
						//respElm.find('.dropdown-toggle').dropdown();
						self.eventPluginElm.find('[name=add]').find('[name=form]').replaceWith(respElm);
					
					},

				    500: function(data, status){
				    	console.error(data);
					}
				}
	        });
			
			return false;
        };
        
		/* Initialize create new item form */
		this.eventPluginElm.find('[name=add]').find('.submit').click(addFunc);
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
		editElm.find('.form-control').each(function(idx, elm){
			origData[$(elm).attr('name')] = $(elm).val();
		});

		editElm.show();

		editElm.find('textarea.autosize').trigger('autosize.resize');
		editElm.find('.form-control:first').focus();
		
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
				
				/* validation error */
				422: function(data, status){
					var respElm = $(data.responseText);
					//respElm.find('.dropdown-toggle').dropdown();
					itemElm.replaceWith(respElm);
					self.initItem(respElm);
					
					respElm.find('div[name=show]').hide();
					respElm.find('div[name=edit]').show();
				
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
		console.log(origData);
		editElm.find('.form-control').each(function(idx, elm){
			if( $.inArray($(elm).attr('name'), origData) ){
				$(elm).val(origData[$(elm).attr('name')]);
			}		
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
	
	return self;
};