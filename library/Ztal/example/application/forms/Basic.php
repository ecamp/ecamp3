<?php
/**
 * Example Form.
 *
 * @category  Namesco
 * @package   Account
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Example Form.
 *
 * @category Namesco
 * @package  Account
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */

class Application_Form_Basic extends Ztal_Form
{
	
	/**
	 * Sets up the form.
	 *
	 * @return void
	 */
	public function init()
	{
		$selectOptions = array(
			'wobble' => 'wobbleLabel',
			'wibble' => 'wibbleLabel',
			'ping' => 'pingLabel',
			'pong' => 'pongLabel',
		);
		
		// Set the attributes of the form
		$this->setAction('/default/index/index');
		
		// Add a select box based on the array above,
		// of type 'select' and name 'selectList'
		$this->addElement('select', 'selectList', array(
			'label' => 'selectSomething',
			'required' => true,
			'multiOptions' => $selectOptions));


		// Create a group of 2 tick boxes
		// first add the individual checkboxes
		$this->addElement('checkbox', 'checkbox1', array(
			'label' => 'checkMe',));

		$this->addElement('checkbox', 'checkbox2', array(
			'label' => 'checkMeToo',));		


		// Now add them to the group
		$this->addDisplayGroup(array('checkbox1', 'checkbox2'),
			'coolCheckboxGroup');

		// Add a textbox
		$this->addElement('text', 'textBox', array(
			'label' => 'typeSomething',
			'required' => true));
										
																				
		// The save button
		$this->addElement('button', 'save', array(
			'type' => 'submit',
			'value' => 'save'));
	}
}
