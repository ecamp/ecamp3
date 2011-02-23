<?php
/**
 * Global default error handler.
 * 
 * Handles global errors across the application.
 *
 * @category  Namesco
 * @package   Default
 * @author    Robert Goldsmith <rgoldsmith@names.co.uk>
 * @copyright 2009-2010 Namesco Limited
 * @license   http://names.co.uk/license Namesco
 */

/**
 * Default error controller.
 * 
 * As well as acting like a normal controller, this controller is used for
 * redirecting un-caught error events across the application.
 *
 * @category Namesco
 * @package  Default
 * @author   Robert Goldsmith <rgoldsmith@names.co.uk>
 */
class ErrorController extends Zend_Controller_Action
{
	
	/**
	 * Handles displaying of errors.
	 *
	 * Handles displaying of errors from anywhere on the site not caught
	 * elsewhere.
	 *
	 * @return void
	 */
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = 'Page not found';
				break;
				
			default:
				// application error 
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = 'Application error';
				break;
		}
		
		$this->view->exceptionMessage = $errors->exception->getMessage();
		$this->view->stackTrace = $errors->exception->getTraceAsString();
		$this->view->requestParameters = var_export($errors->request->getParams(), true);
		$this->view->productionEnvironment = (APPLICATION_ENV == 'production');
	}

}

