<?php
/*
 * Copyright (C) 2011 Pirmin Mattmann
 *
 * This file is part of eCamp.
 *
 * eCamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * eCamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with eCamp.  If not, see <http://www.gnu.org/licenses/>.
 */

class WebApp_ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
    	if(get_class($this->view) == 'Zend_View'){
    		$view = new Zend_View();
    		
    		$view->setEncoding('UTF-8');
    		$view->doctype('XHTML1_STRICT');
    		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    		
    		// 		$view->headLink()->appendStylesheet('/css/blueprint/screen.css', 'screen, projection');
    		// 		$view->headLink()->appendStylesheet('/css/blueprint/ie.css', 'screen, projection', 'lt IE 8');
    		// 		$view->headLink()->appendStylesheet('/css/blueprint/print.css', 'print');
    		
    		$view->headLink()->appendStylesheet('/css/blueprint/plugins/fancy-type/screen.css', 'screen, projection');
    		$view->headLink()->appendStylesheet('/css/blueprint/plugins/buttons/screen.css', 'screen, projection');
    		
    		$view->headLink()->appendStylesheet('/css/main.css');
    		
    		
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    		$viewRenderer->setView($view);
    		
    		return $view;
    	}
    	
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
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
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
        
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

