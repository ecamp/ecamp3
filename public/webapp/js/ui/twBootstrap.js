
var twBootstrap = angular.module('twBootstrap', ['ui']);

twBootstrap.directive('inputText', function(){
	return {
		restrict:'E',
		
		template: 	"<div style='display: table' class='input-prepend'>" +
						"<div style='display: table-row'>" +
							"<div style='display: table-cell; width: 110px'>" +
								"<div class='add-on' style='width: 100%; text-align: left;'>{{label}}</div>" +
							"</div>" +
							"<div style='display: table-cell; position: relative'>" +
								"<div class='img_status' ng-class='$status.icon_class' ng-show='showstatus' style='position: absolute; right: -30px; top: 7px; opacity: 0.2; z-index: 3' />" +
								"<input type='text' name='{{name}}' ng-disabled='disabled' ng-model='currValue' style=' padding-right: 30px; width: 100%' />" +
							"</div>" +
							"<div style='display: table-cell; width: 30px;'></div>" +
						"</div>" +
						
						"<br />" +
						"<pre>origValue: <input type='text' ng-model='origValue' /></pre>" +
						"<pre>currValue: <input type='text' ng-model='currValue' /></pre>" +
						"<pre>dirty: {{dirty}}</pre>" +
						"<pre>conflict: {{conflict}}</pre>" +
						"<pre>status: {{status}}</pre>" +
					"</div>",
					
		replace: true,
		
		scope: {
			label: 		'@label',
			origValue: 	'=ngModel',
			name:		'@name',
			readonly:	'@readonly',
			disabled:	'@disabled'
		},
		
		link: function(scope, element, attrs) {
			
			scope.dirty = false;
			scope.conflict = false;
			scope.showstatus = true;
			scope.currValue = scope.origValue;
			
			scope.status = 'ok';
			scope.$status = {};
			scope.$status.icon_class = 'icon-ok';
			scope.$status.img = $(element).find('div.img_status');
			scope.$status.img.tooltip({ placement: 'bottom', title: '' });
			
			scope.$status.setTitle = function(title){
				scope.$status.img.attr('data-original-title', title).tooltip('fixTitle');
			};
			
			scope.calcState = function(newVal, oldVal, scope){

				scope.disabled = 
					scope.readonly !== false && scope.readonly !== undefined ||
					scope.disabled !== false && scope.disabled !== undefined;
				scope.showstatus = !scope.disabled;
				
				if(scope.conflict)	{	scope.status = 'conflict';	}
				else if(scope.dirty){	scope.status = 'dirty';		}
				else				{	scope.status = 'ok';		}
			};
			
			scope.$watch('status', function(newVal, oldVal, scope){
				switch(newVal){
					case 'ok':
						scope.$status.icon_class= 'icon-ok';
						scope.$status.setTitle('Gespeichert');
						break;
						
					case 'dirty':
						scope.$status.icon_class= 'icon-pencil';
						scope.$status.setTitle('Nicht gespeichert');
						break;
						
					case 'conflict':
						scope.$status.icon_class = 'icon-warning-sign';
						scope.$status.setTitle('Conflict');
						break;
					
					default: 
						scope.$status.icon_class = '';
						scope.$status.setTitle('');
				}
			});
			
			
			scope.$watch('currValue', function(newVal, oldVal, scope){
				scope.dirty = scope.dirty || (newVal != scope.origValue);
			});
			
			scope.$watch('origValue', function(newVal, oldVal, scope){
				if(! scope.dirty)	{	scope.currValue = newVal;	}
				else				{	scope.conflict = true;		}
			});

			scope.$watch('dirty', scope.calcState);
			scope.$watch('conflict', scope.calcState);
			scope.$watch('readonly', scope.calcState);
		}
	};
});



twBootstrap.directive('inputNumber', function(){
	return {
		restrict:'E',
		
		template: 	"<div style='display: table' class='input-prepend'>" +
						"<div style='display: table-row'>" +
							"<div style='display: table-cell; width: 110px'>" +
								"<div class='add-on' style='width: 100%; text-align: left;'>{{label}}</div>" +
							"</div>" +
							"<div style='display: table-cell; position: relative'>" +
								"<div class='img_status' ng-class='$status.icon_class' ng-show='showstatus' style='position: absolute; right: -30px; top: 7px; opacity: 0.2; z-index: 3' />" +
								"<input type='number' name='{{name}}' ng-disabled='disabled' ng-model='currValue' style='padding-right: 30px; width: 100%' />" +
							"</div>" +
							"<div style='display: table-cell; width: 30px;'></div>" +
						"</div>" +
						
//						"<br />" +
//						"<pre>origValue: <input type='text' ng-model='origValue' /></pre>" +
//						"<pre>currValue: <input type='text' ng-model='currValue' /></pre>" +
//						"<pre>dirty: {{dirty}}</pre>" +
//						"<pre>conflict: {{conflict}}</pre>" +
//						"<pre>status: {{status}}</pre>" +
					"</div>",
					
		replace: true,
		
		scope: {
			label: 		'@label',
			origValue: 	'=ngModel',
			name:		'@name',
			readonly:	'@readonly',
			disabled:	'@disabled'
		},
		
		link: function(scope, element, attrs) {
			
			scope.dirty = false;
			scope.conflict = false;
			scope.showstatus = true;
			scope.currValue = scope.origValue;
			
			scope.status = 'ok';
			scope.$status = {};
			scope.$status.icon_class = 'icon-ok';
			scope.$status.img = $(element).find('div.img_status');
			scope.$status.img.tooltip({ placement: 'bottom', title: '' });
			
			
			scope.$status.setTitle = function(title){
				scope.$status.img.attr('data-original-title', title).tooltip('fixTitle');
			};
			
			scope.calcState = function(newVal, oldVal, scope){

				scope.disabled = 
					scope.readonly !== false && scope.readonly !== undefined ||
					scope.disabled !== false && scope.disabled !== undefined;
				scope.showstatus = !scope.disabled;
				
				if(scope.conflict)	{	scope.status = 'conflict';	}
				else if(scope.dirty){	scope.status = 'dirty';		}
				else				{	scope.status = 'ok';		}
			};
			
			scope.$watch('status', function(newVal, oldVal, scope){
				switch(newVal){
					case 'ok':
						scope.$status.icon_class= 'icon-ok';
						scope.$status.setTitle('Gespeichert');
						break;
						
					case 'dirty':
						scope.$status.icon_class= 'icon-pencil';
						scope.$status.setTitle('Nicht gespeichert');
						break;
						
					case 'conflict':
						scope.$status.icon_class = 'icon-warning-sign';
						scope.$status.setTitle('Conflict');
						break;
					
					default: 
						scope.$status.icon_class = '';
						scope.$status.setTitle('');
				}
			});
			
			
			scope.$watch('currValue', function(newVal, oldVal, scope){
				scope.dirty = scope.dirty || (newVal != scope.origValue);
			});
			
			scope.$watch('origValue', function(newVal, oldVal, scope){
				if(! scope.dirty)	{	scope.currValue = newVal;	}
				else				{	scope.conflict = true;		}
			});

			scope.$watch('dirty', scope.calcState);
			scope.$watch('conflict', scope.calcState);
			scope.$watch('readonly', scope.calcState);
		}
	};
});



twBootstrap.directive('inputDate', function(){
	return {
		restrict:'E',
		
		template: 	"<div style='display: table' class='input-prepend'>" +
						"<div style='display: table-row'>" +
							"<div style='display: table-cell; width: 110px'>" +
								"<div class='add-on' style='width: 100%; text-align: left;'>{{label}}</div>" +
							"</div>" +
							"<div style='display: table-cell; position: relative'>" +
								"<div class='img_status' ng-class='$status.icon_class' ng-show='showstatus' style='position: absolute; right: -30px; top: 7px; opacity: 0.2; z-index: 3' />" +
								"<input type='text' ui-date='{ dateFormat: \"d. MM yy\" }' ui-date-format='dd.mm.yy' class='val' name='{{name}}' ng-disabled='disabled' ng-model='currValue' style='padding-right: 30px; width: 100%;' />" +
							"</div>" +
							"<div style='display: table-cell; width: 30px;'></div>" +
						"</div>" +
						
//						"<br />" +
//						"<pre>origValue: <input type='text' ng-model='origValue' /></pre>" +
//						"<pre>currValue: <input type='text' ng-model='currValue' /></pre>" +
//						"<pre>dirty: {{dirty}}</pre>" +
//						"<pre>conflict: {{conflict}}</pre>" +
//						"<pre>status: {{status}}</pre>" +
					"</div>",
					
		replace: true,
		
		scope: {
			label: 		'@label',
			origValue: 	'=ngModel',
			name:		'@name',
			readonly:	'@readonly',
			disabled:	'@disabled'
		},
		
		link: function(scope, element, attrs) {
			
			scope.dirty = false;
			scope.conflict = false;
			scope.showstatus = true;
			scope.currValue = scope.origValue;
			
			scope.status = 'ok';
			scope.$status = {};
			scope.$status.icon_class = 'icon-ok';
			scope.$status.img = $(element).find('div.img_status');
			scope.$status.img.tooltip({ placement: 'bottom', title: '' });
			
			scope.$status.setTitle = function(title){
				scope.$status.img.attr('data-original-title', title).tooltip('fixTitle');
			};
			
//			$(element).find("input.val").datepicker({
//				changeMonth: true,
//				changeYear: true,
//				dateFormat: 'dd.mm.yy',
//				showOtherMonths: true,
//				onSelect: function(val, obj){ scope.$apply(scope.currValue = new Date(val));	}
//			});
			
			scope.calcState = function(newVal, oldVal, scope){
				scope.disabled = 
					scope.readonly !== false && scope.readonly !== undefined ||
					scope.disabled !== false && scope.disabled !== undefined;
				scope.showstatus = !scope.disabled;
				
				if(scope.conflict)	{	scope.status = 'conflict';	}
				else if(scope.dirty){	scope.status = 'dirty';		}
				else				{	scope.status = 'ok';		}
			};
			
			scope.$watch('status', function(newVal, oldVal, scope){
				switch(newVal){
					case 'ok':
						scope.$status.icon_class= 'icon-ok';
						scope.$status.setTitle('Gespeichert');
						break;
						
					case 'dirty':
						scope.$status.icon_class= 'icon-pencil';
						scope.$status.setTitle('Nicht gespeichert');
						break;
						
					case 'conflict':
						scope.$status.icon_class = 'icon-warning-sign';
						scope.$status.setTitle('Conflict');
						break;
					
					default: 
						scope.$status.icon_class = '';
						scope.$status.setTitle('');
				}
			});
			
			scope.$watch('currValue', function(newVal, oldVal, scope){
				scope.dirty = scope.dirty || (newVal != scope.origValue);
			});
			
			scope.$watch('origValue', function(newVal, oldVal, scope){
				if(! scope.dirty)	{	scope.currValue = newVal;	}
				else				{	scope.conflict = true;		}
			});

			scope.$watch('dirty', scope.calcState);
			scope.$watch('conflict', scope.calcState);
			scope.$watch('readonly', scope.calcState);
		}
	};
});