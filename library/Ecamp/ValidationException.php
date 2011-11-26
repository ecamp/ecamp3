<?php

namespace Ecamp;

/**
 * Validation exception
 */
class ValidationException extends \Exception{
	public $form;
	
	public function __construct( \Zend_Form $form = null ){
		$this->form = $form;
	}
}