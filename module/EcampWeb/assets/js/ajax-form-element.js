
(function(ngApp){

    /*

        [AjaxForm]
         - Url
         - State
         - Controls
         + Reset
         + Submit

        [AjaxFormControl]
         - Elements
         - OrigValues
         + Reset

     */

    ngApp.directive('ajaxForm', ['$filter', function($filter){
        var $dateFilter = $filter('date');

        var AjaxForm = function(url, controls, feedbacks){
            this.url = url;
            this.controls = controls;
            this.feedbacks = feedbacks;
            this.state = 'orig';

            this.savedTimeout = null;
            this.saveTimeout = null;
            this.saveDelay = 150;

            this.SaveOrigValue();
            this.SetupEvents();

            this.feedbacks.empty();
        };

        AjaxForm.prototype.SetupEvents = function(){
            angular.forEach(this.controls, function(ctrl){
                ctrl.Focus(this.OnFocus.bind(this));
                ctrl.Edit(this.OnEdit.bind(this));
                ctrl.Save(this.OnSave.bind(this));
                ctrl.Cancel(this.OnCancel.bind(this));
            }, this);
        };

        AjaxForm.prototype.OnFocus = function(event){
            this.StopSave();
            this.CheckAndSetDirty();
        };

        AjaxForm.prototype.OnEdit = function(event){
            this.CheckAndSetDirty();
        };

        AjaxForm.prototype.OnSave = function(event){
            return this.BeginSave();
        };

        AjaxForm.prototype.OnCancel = function(event){
            this.Undo();
        };


        /** @return {boolean} */
        AjaxForm.prototype.IsOrig = function(){
            return this.state == 'orig';
        };

        /** @return {boolean} */
        AjaxForm.prototype.IsDirty = function(){
            return this.state == 'dirty';
        };

        /** @return {boolean} */
        AjaxForm.prototype.IsSaving = function(){
            return this.state == 'saving';
        };

        /** @return {boolean} */
        AjaxForm.prototype.IsSaved = function(){
            return this.state == 'saved';
        };

        /** @return {boolean} */
        AjaxForm.prototype.IsFailed = function(){
            return this.state == 'failed';
        };

        /** @return {boolean} */
        AjaxForm.prototype.CheckDirty = function(){
            var isDirty = this.IsDirty();

            if(this.IsOrig() || this.IsSaved() || this.IsFailed()){
                angular.forEach(this.controls, function(ctrl){
                    isDirty = isDirty || ctrl.CheckDirty();
                });
            }

            return isDirty;
        };

        AjaxForm.prototype.CheckAndSetDirty = function(){
            if(this.CheckDirty()){
                this.SetDirty();
            }
        };

        AjaxForm.prototype.SetOrig = function(force){
            force = force || false;

            if(force || !this.IsDirty()) {
                if(this.savedTimeout){
                    clearTimeout(this.savedTimeout);
                    this.savedTimeout = null;
                }

                this.feedbacks.empty();
                this.feedbacks.css('width', '0px');
                this.SetFeedbackSize(0);

                this.state = 'orig';
            }
        };

        AjaxForm.prototype.SetDirty = function(){
            if(this.savedTimeout){
                clearTimeout(this.savedTimeout);
                this.savedTimeout = null;
            }

            this.feedbacks.empty();
            this.feedbacks.css('width', '70px');
            this.feedbacks.css('pointer-events', 'all');
            this.SetFeedbackSize(70);

            angular.forEach(this.feedbacks, function(feedback) {
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

                btnSave.click(this.Save.bind(this));
                btnCancel.click(this.Undo.bind(this));

                $(feedback).append(btnGroup);
            }, this);

            this.state = 'dirty';
        };

        AjaxForm.prototype.SetSaving = function() {
            if(this.savedTimeout){
                clearTimeout(this.savedTimeout);
                this.savedTimeout = null;
            }

            this.feedbacks.empty();
            this.feedbacks.css('width', '40px');
            this.SetFeedbackSize(40);

            angular.forEach(this.feedbacks, function(feedback){
                var icon = $('<i class="fa fa-spinner"></i>');
                icon.css('color', '#428bca');
                icon.css('font-size', '20px');
                icon.css('margin-top', '8px');
                icon.css('animation', 'spin-step 0.7s steps(8, end) infinite');
                icon.css('-webkit-animation', 'spin-step 0.7s steps(8, end) infinite');

                $(feedback).append(icon);
            });

            this.state = 'saving';
        };

        AjaxForm.prototype.SetSaved = function(){
            this.feedbacks.empty();
            this.feedbacks.css('width', '40px');
            this.SetFeedbackSize(40);

            angular.forEach(this.feedbacks, function(feedback){
                var icon = $('<i class="fa fa-check"></i>');
                icon.css('color', '#4cae4c');
                icon.css('font-size', '15px');

                $(feedback).append(icon);
            });

            this.state = 'saved';
        };

        AjaxForm.prototype.SetFailed = function(){
            this.feedbacks.empty();
            this.feedbacks.css('width', '40px');
            this.SetFeedbackSize(40);

            angular.forEach(this.feedbacks, function(feedback){
                var icon = $('<i class="fa fa-exclamation-triangle"></i>');
                icon.css('color', '#f0ad4e');
                icon.css('font-size', '20px');
                icon.css('margin-top', '8px');

                $(feedback).append(icon);
            });

            this.state = 'failed';
        };


        AjaxForm.prototype.GetValue = function(){
            var formData = {};
            angular.forEach(this.controls, function(ctrl, name){
                this[name] = ctrl.GetValue();
            }, formData);
            return formData;
        };

        AjaxForm.prototype.SaveOrigValue = function() {
            angular.forEach(this.controls, function (ctrl) {
                ctrl.SaveOrigValue();
            });
        };

        AjaxForm.prototype.Disable = function() {
            angular.forEach(this.controls, function (ctrl) {
                ctrl.Disable();
            }, this);
        };

        AjaxForm.prototype.Enable = function() {
            angular.forEach(this.controls, function (ctrl) {
                ctrl.Enable();
            }, this);
        };

        AjaxForm.prototype.SetFeedbackSize = function(size){
            angular.forEach(this.controls, function (ctrl) {
                ctrl.SetFeedbackSize(size);
            }, this);
        };

        AjaxForm.prototype.Undo = function(){
            if(this.IsDirty()){
                clearTimeout(this.saveTimeout);

                angular.forEach(this.controls, function(ctrl){
                    ctrl.Reset();
                }, this);
                this.SetOrig(true);

                /* todo: Focus any Control? */
            }
        };

        /** @return {boolean} */
        AjaxForm.prototype.BeginSave = function(){
            if(this.CheckDirty()){
                this.saveTimeout = setTimeout(this.Save.bind(this), this.saveDelay);
                return true;
            }
            return false;
        };

        AjaxForm.prototype.StopSave = function(){
            if(this.saveTimeout != null){
                clearTimeout(this.saveTimeout);
            }
            this.saveTimeout = null;
        };

        AjaxForm.prototype.Save = function(){
            this.SetSaving();
            var formData = this.GetValue();

            this.Disable();

            jQuery.ajax({
                type: 'PUT',
                url: this.url,
                data: formData,
                cache: false,
                context: this,
                orientation: 'bottom'
            })
                .done(this.Saved)
                .fail(this.Failed);
        };

        AjaxForm.prototype.Saved = function(){
            this.SaveOrigValue();
            this.Enable();
            this.SetSaved();

            this.savedTimeout = setTimeout(this.SetOrig.bind(this), 2000);
        };

        AjaxForm.prototype.Failed = function(){
            this.Enable();
            this.SetFailed();
        };



        var AjaxFormControl = function(element){
            this.element = element;
            this.origValue = null;
        };



        var AjaxFormControlRadio = function(elements){
            this.element = elements;

            angular.forEach(this.element, function(elm){
                var $elm = $(elm);
                $elm.focus(function(){ $elm.prop('checked', true); });
            }, this);

            this.SetValue(this.element.filter('[checked]').val());
        };

        AjaxFormControlRadio.prototype = new AjaxFormControl();
        AjaxFormControlRadio.prototype.constructor = AjaxFormControlRadio;
        AjaxFormControlRadio.prototype.parent = AjaxFormControl.prototype;



        var AjaxFormControlDate = function(elements){
            this.element = elements;
            this.element.attr('type', 'text');

            var format = this.element.attr('format') || 'yyyy-mm-dd';
            var value = this.element.attr('value') || '';

            this.datepicker = this.element.datepicker({
                format: format,
                weekStart: 1,
                autoclose: true,
                clearBtn: true
            });

            this.SetValue(value);
        };

        AjaxFormControlDate.prototype = new AjaxFormControl();
        AjaxFormControlDate.prototype.constructor = AjaxFormControlDate;
        AjaxFormControlDate.prototype.parent = AjaxFormControl.prototype;



        var AjaxFormControlSelect = function(elements){
            this.element = elements;

            console.log(this);
        };

        AjaxFormControlSelect.prototype = new AjaxFormControl();
        AjaxFormControlSelect.prototype.constructor = AjaxFormControlSelect;
        AjaxFormControlSelect.prototype.parent = AjaxFormControl.prototype;



        AjaxFormControl.Create = function(elements){
            if(!elements || elements.length == 0){
                throw "Now Element given";
            }

            var elementTypes = [];
            angular.forEach(elements, function(element){
                elementTypes.push(this.GetType(element));
            }, this);

            var elementType = $.unique(elementTypes);

            if(elementType.length != 1){
                throw "Element Type is ambiguous";
            }

            elementType = elementType[0];

            console.log(elementType);

            elements.focus(function(event){
                AjaxFormControl.LastFocusedControl = event.target;
            });

            switch(elementType.toLowerCase()){
                case 'radio':
                    return new AjaxFormControlRadio(elements);

                case 'date':
                    return new AjaxFormControlDate(elements);

                case 'select':
                    return new AjaxFormControlSelect(elements);

                default:
                    return new AjaxFormControl(elements);
            }
        };

        /** @return {string} */
        AjaxFormControl.GetType = function(element){
            var $element = $(element);

            if($element.is('input')){
                return $element.attr('type') || 'text';
            }

            if($element.is('select')){
                return 'select';
            }

            return 'unknown';
        };

        AjaxFormControl.LastFocusedControl = null;

        AjaxFormControl.prototype.GetValue = function(){
            return angular.isElement(this.element) ? this.element.val() : null;
        };

        AjaxFormControl.prototype.SetValue = function(val){
            angular.isElement(this.element) && this.element.val(val);
        };

        AjaxFormControl.prototype.SaveOrigValue = function(){
            this.origValue = this.GetValue();
        };

        AjaxFormControl.prototype.Reset = function(){
            this.SetValue(this.origValue);
        };

        AjaxFormControl.prototype.Disable = function(){
            this.element.attr('disabled', true);
        };

        AjaxFormControl.prototype.Enable = function(){
            this.element.removeAttr('disabled');

            if($(':focus').length == 0 && this.element.is(AjaxFormControl.LastFocusedControl)){
                this.element.focus();
            }
        };

        /** @return {boolean} */
        AjaxFormControl.prototype.CheckDirty = function(){
            return this.origValue != this.GetValue();
        };

        AjaxFormControl.prototype.Focus = function(h){
            this.element.focus(h);
        };

        AjaxFormControl.prototype.Edit = function(h){
            this.element.change(h);
            this.element.keyup(h);
        };

        AjaxFormControl.prototype.Save = function(h){
            this.element.blur(h);
        };

        AjaxFormControl.prototype.Cancel = function(h){
            this.element.keydown(function(event){
                if(event.keyCode == 27){ h(event); }
            });
        };

        AjaxFormControl.prototype.SetFeedbackSize = function(size){
            size = size + 2.5;
            this.element.css('padding-right', size + 'px')
        };




        /** @return {null} */
        AjaxFormControlRadio.prototype.GetValue = function(){
            if(angular.isElement(this.element)){
                var checkedElement = this.element.filter(':checked');
                return checkedElement.length == 1 ? checkedElement.val() : null;
            }
            return null;
        };

        AjaxFormControlRadio.prototype.SetValue = function(val){
            var elm = this.element.filter('[value=' + val + ']');
            elm.prop('checked', true);
            elm.parent().filter('label').addClass('active');

            elm = this.element.filter('[value!=' + val + ']');
            elm.prop('checked', false);
            elm.parent().filter('label').removeClass('active');
        };

        AjaxFormControlRadio.prototype.Disable = function(){
            this.element.parent().filter('label').css('pointer-events', 'none');
        };

        AjaxFormControlRadio.prototype.Enable = function(){
            this.element.parent().filter('label').css('pointer-events', '');
        };

        AjaxFormControlRadio.prototype.Focus = function(h){};
        AjaxFormControlRadio.prototype.Edit = function(h){};
        AjaxFormControlRadio.prototype.Save = function(h){ this.element.change(h); };
        AjaxFormControlRadio.prototype.Cancel = function(h){};



        AjaxFormControlDate.prototype.GetValue = function(){
            var d = this.element.datepicker('getDate');
            return !isNaN(d) ? $dateFilter(d, 'yyyy-MM-dd') : '';
        };

        AjaxFormControlDate.prototype.SetValue = function(val){
            var d = new Date(val);
            this.element.datepicker('setDate', !isNaN(d) ? d : null);
        };

        AjaxFormControlDate.prototype.Focus = function(h){};
        AjaxFormControlDate.prototype.Edit = function(h){};
        AjaxFormControlDate.prototype.Save = function(h){
            var save = function(event){
                if(h(event)){
                    //this.element.focus();
                    this.element.datepicker('hide');
                }
            }.bind(this);

            this.element.change(save);
            this.datepicker.on('clearDate', save);
        };
        AjaxFormControlDate.prototype.Cancel = function(h){};



        AjaxFormControlSelect.prototype.Focus = function(h){};
        AjaxFormControlSelect.prototype.Edit = function(h){};
        AjaxFormControlSelect.prototype.Save = function(h){
            this.element.change(h);
        };
        AjaxFormControlSelect.prototype.Cancel = function(h){};


        return {
            restrict: 'E',
            scope: true,

            link: function($scope, $element, $attrs, $ctrl) {

                var formControls = $element.find('.form-control');
                var ajaxFormControls = {};

                if(formControls.length > 0) {
                    angular.forEach(formControls, function (formControl) {
                        var controlName = $(formControl).attr('name');
                        if (controlName && controlName.length > 0 && ajaxFormControls[controlName] == undefined) {
                            ajaxFormControls[controlName] =
                                AjaxFormControl.Create($element.find('[name=' + controlName + '].form-control'));
                        }
                    });
                }

                var ajaxFeedbackContainers = $element.find('.form-control-feedback');

                var action = $attrs['action'];
                new AjaxForm(action, ajaxFormControls, ajaxFeedbackContainers);
            }
        }
    }]);

}(window.ecamp.ngApp));
