<?php

class Zend_View_Helper_Thisurl extends Zend_View_Helper_Abstract
{
	public $view;

    public function thisurl( $urlOptions, $name = null, $reset = false, $encode = true)
    {
	    if( ! isset($urlOptions['controller']))
	       $urlOptions['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			       
        return $this->view->url( $urlOptions, $name, $reset, $encode);
    }

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

}