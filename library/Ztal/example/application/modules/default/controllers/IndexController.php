<?php
/**
 * Default index controller.
 * 
 * Controller to handle requests with no module, / and /index.php requests.
 *
 * @category  Namesco
 * @package   Default
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Default index controller.
 * 
 * As well as acting like a normal controller, this controller also handles
 * requests on urls such as / and /index.php
 * through the indexAction method
 *
 * @category Namesco
 * @package  Default
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class IndexController extends Zend_Controller_Action
{
	/**
	 * The index action.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		// Create the form
		$exampleForm = new Application_Form_Basic();
				
		// Check if there is some post data to work with
		if ($this->_request->isPost()) {
		
			// If there is post data, try to validate the form.
			if ($exampleForm->isValid($this->_request->getPost())) {
			
				// Input data is valid so grab all the values
				$submittedFormDetails = $exampleForm->getValues();
				var_dump($submittedFormDetails);
				exit();
			}
		}

		// If we get here either there was no post data or there was an error
		
		// Setup some defaults
		$defaults = array();
		$defaults['checkBox1'] = 1;
		$defaults['checkBox2'] = 0;
		$defaults['selectList'] = 'wibble';
		$defaults['textBox'] = 'Hello';
		$exampleForm->setDefaults($defaults);
		
		// Add the form object to the view
		$this->view->exampleForm = $exampleForm;
		
		// Set a title for the page
		$this->view->headTitle('Communication Option Details');
	}
}

