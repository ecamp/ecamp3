<?php
return array(

	'factories' => array(
/*REPOSITORY-FACTORY*/
	),
	
	'initializers' => array(
		function($instance, $sm){
			
			if(! is_object($instance)){
				return;
			}
			
			foreach(class_uses($instance) as $trait){
				switch($trait){
					
/*REPOSITORY-CASE*/
					default:
						break;
				}
			}
		}
	)
);