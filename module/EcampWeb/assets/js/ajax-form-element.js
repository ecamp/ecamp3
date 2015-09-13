
jQuery(function($){

    function AjaxFormElementController(container){
        this.$container = $(container);
        this.$control = this.$container.find('.form-control');
        this.$feedbackContainer = this.$container.find('.feedback-container');

        this.$state = 'orig';
        this.$origValue = this.getValue();
        this.$saveTimeout = null;
        this.$saveDelay = 100;

        this.$backendStorageUrl = this.$container.attr('data-backend');
    }

    AjaxFormElementController.prototype.getValue = function(){
        return this.$control.val();
    };

    AjaxFormElementController.prototype.setValue = function(value){
        this.$control.val(value);
    };

    AjaxFormElementController.prototype.getOrigValue = function(){
        return this.$origValue;
    };


    AjaxFormElementController.prototype.setOrig = function(force){
        force = force || false;

        if(force || !this.isDirty()) {
            this.$feedbackContainer.empty();
            this.$feedbackContainer.css('width', '0px');
            this.$control.css('padding-right', '12px');
            this.$state = 'orig';
        }
    };

    AjaxFormElementController.prototype.setDirty = function(){
        this.$feedbackContainer.empty();
        this.$feedbackContainer.css('width', '70px');
        this.$feedbackContainer.css('pointer-events', 'all');
        this.$control.css('padding-right', '72.5px');

        var iconSave = $('<i class="fa fa-check"></i>');
        iconSave.css('color', 'white');
        iconSave.css('font-size', '15px');
        var btnSave = $('<button class="btn btn-xs btn-success btn-ajax-feedback"></button>');
        btnSave.css('width', '30px');
        btnSave.attr('tabindex', '-1');
        btnSave.append(iconSave);

        var iconCancel = $('<i class="fa fa-times"></i>');
        iconCancel.css('color', 'white');
        iconCancel.css('font-size', '15px');
        var btnCancel = $('<button class="btn btn-xs btn-danger btn-ajax-feedback"></button>');
        btnCancel.css('width', '30px');
        btnCancel.attr('tabindex', '-1');
        btnCancel.append(iconCancel);

        var btnGroup = $('<div class="btn-group"></div>');
        btnGroup.append(btnSave);
        btnGroup.append(btnCancel);

        btnSave.click(this.save.bind(this));
        btnCancel.click(this.undo.bind(this));
        btnSave.focus(this.onFocus.bind(this));
        btnCancel.focus(this.onFocus.bind(this));
        btnSave.blur(this.onBlur.bind(this));
        btnCancel.blur(this.onBlur.bind(this));
        btnSave.keydown(this.onKeyDown.bind(this));
        btnCancel.keydown(this.onKeyDown.bind(this));

        this.$feedbackContainer.append(btnGroup);
        this.$state = 'dirty';
    };

    AjaxFormElementController.prototype.setSaving = function(){
        this.$feedbackContainer.empty();
        this.$feedbackContainer.css('width', '40px');
        this.$control.css('padding-right', '42.5px');

        var iconWait = $('<i class="fa fa-spinner"></i>');
        iconWait.css('color', '#428bca');
        iconWait.css('font-size', '20px');
        iconWait.css('margin-top', '8px');
        iconWait.css('animation', 'spin-step 0.7s steps(8, end) infinite');
        iconWait.css('-webkit-animation', 'spin-step 0.7s steps(8, end) infinite');

        this.$feedbackContainer.append(iconWait);
        this.$state = 'saving';
    };

    AjaxFormElementController.prototype.setSaved = function(){
        this.$feedbackContainer.empty();
        this.$feedbackContainer.css('width', '40px');
        this.$control.css('padding-right', '42.5px');

        var iconCheck = $('<i class="fa fa-check"></i>');
        iconCheck.css('color', '#4cae4c');
        iconCheck.css('font-size', '15px');
        var btnCheck = $('<button class="btn btn-xs btn-noborder btn-ajax-feedback"></button>');
        btnCheck.css('background-color', 'transparent');
        btnCheck.css('cursor', 'default');
        btnCheck.css('width', '30px');
        btnCheck.attr('tabindex', '-1');
        btnCheck.append(iconCheck);

        this.$feedbackContainer.append(btnCheck);
        this.$state = 'saved';

        if($(':focus').length == 0 /* && !this.$control.is(':radio') */) {
            this.$control.focus();
        }
    };

    AjaxFormElementController.prototype.setFailed = function(){
        this.$feedbackContainer.empty();
        this.$feedbackContainer.css('width', '40px');
        this.$control.css('padding-right', '42.5px');

        var iconCheck = $('<i class="fa fa-exclamation-triangle"></i>');
        iconCheck.css('color', '#f0ad4e');
        iconCheck.css('font-size', '20px');
        var btnCheck = $('<button class="btn btn-xs btn-noborder btn-ajax-feedback"></button>');
        btnCheck.css('background-color', 'transparent');
        btnCheck.css('cursor', 'default');
        btnCheck.css('width', '30px');
        btnCheck.attr('tabindex', '-1');
        btnCheck.append(iconCheck);

        this.$feedbackContainer.append(btnCheck);
        this.$state = 'failed';
    };

    AjaxFormElementController.prototype.isOrig = function(){
        return this.$state == 'orig';
    };

    AjaxFormElementController.prototype.isDirty = function(){
        return this.$state == 'dirty';
    };

    AjaxFormElementController.prototype.isSaving = function(){
        return this.$state == 'saving';
    };

    AjaxFormElementController.prototype.isSaved = function(){
        return this.$state == 'saved';
    };

    AjaxFormElementController.prototype.isFailed = function(){
        return this.$state == 'failed';
    };

    AjaxFormElementController.prototype.checkDirty = function(){
        if(this.isOrig() || this.isSaved() || this.isFailed()){
            if(this.getValue() != this.getOrigValue()){
                this.setDirty();
            }
        }
    };

    AjaxFormElementController.prototype.undo = function(){
        if(this.isDirty()){
            this.setValue(this.getOrigValue());
            this.setOrig(true);
            this.$control.focus();
        }
    };

    AjaxFormElementController.prototype.beginSave = function(){
        if(this.isDirty()){
            this.$saveTimeout = setTimeout(this.save.bind(this), this.$saveDelay);
        }
    };

    AjaxFormElementController.prototype.stopSave = function(){
        if(this.$saveTimeout != null){
            clearTimeout(this.$saveTimeout);
        }
        this.$saveTimeout = null;
    };

    AjaxFormElementController.prototype.save = function(){
        this.setSaving();

        var inputData = this.$control.serializeArray();
        var formData = {};
        $.each(inputData, function(idx, data){
            formData[data.name] = data.value;
        });

        this.$control.attr('disabled', true);

        $.ajax({
            type: 'PUT',
            url: this.$backendStorageUrl,
            data: formData,
            cache: false,
            context: this
        })
        .done(this.saved)
        .fail(this.failed);
    };

    AjaxFormElementController.prototype.saved = function(){
        this.$origValue = this.getValue();
        this.$control.removeAttr('disabled');
        this.setSaved();

        setTimeout(this.setOrig.bind(this), 2000);
    };

    AjaxFormElementController.prototype.failed = function(){
        this.$control.removeAttr('disabled');
        this.setFailed();
    };

    AjaxFormElementController.prototype.onFocus = function(event){
        this.stopSave();
        this.checkDirty();
    };

    AjaxFormElementController.prototype.onBlur = function(event){
        this.beginSave();
    };

    AjaxFormElementController.prototype.onKeyDown = function(event){
        if(event.keyCode == 27){
            this.undo();
        }
    };

    AjaxFormElementController.prototype.onKeyUp = function(event){
        this.checkDirty();
    };



    AjaxFormElementController.getInstance = function(element){
        var $element = $(element);
        var $container = $element.closest('.ajax-form-element');

        var controller = $container.data('ajax-form-element-controller');
        if(!controller){
            controller = new AjaxFormElementController($container);
            $container.data('ajax-form-element-controller', controller);
        }

        return controller;
    };

    AjaxFormElementController.onFocus = function(event){
        AjaxFormElementController.getInstance(this).onFocus(event);
    };

    AjaxFormElementController.onBlur = function(event){
        AjaxFormElementController.getInstance(this).onBlur(event);
    };

    AjaxFormElementController.onKeyDown = function(event){
        AjaxFormElementController.getInstance(this).onKeyDown(event);
    };

    AjaxFormElementController.onKeyUp = function(event){
        AjaxFormElementController.getInstance(this).onKeyUp(event);
    };

    AjaxFormElementController.onChange = function(event){
        if($(this).is(':radio')){
            var instance = AjaxFormElementController.getInstance(this);

            instance.$saveTimeout = setTimeout(instance.save.bind(instance), this.$saveDelay);
        }
    };

    /*
    var $document = $(document);
    $document.on('focus', '.ajax-form-element .form-control', AjaxFormElementController.onFocus);
    $document.on('blur', '.ajax-form-element .form-control', AjaxFormElementController.onBlur);
    $document.on('keydown', '.ajax-form-element .form-control', AjaxFormElementController.onKeyDown);
    $document.on('keyup', '.ajax-form-element .form-control', AjaxFormElementController.onKeyUp);
    $document.on('change', '.ajax-form-element .form-control', AjaxFormElementController.onChange);
    */
});




