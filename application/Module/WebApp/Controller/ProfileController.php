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

class WebApp_ProfileController extends \WebApp\Controller\BaseController
{

    public function init()
    {
	    Zend_Layout::getMvcInstance()->setLayout('layout');

	    parent::init();
    }

    public function indexAction()
    {
    	$this->view->headLink()->appendStylesheet("http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css");
    	$this->view->headLink()->appendStylesheet("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css");
    	$this->view->headLink()->appendStylesheet("https://raw.github.com/angular-ui/angular-ui/master/build/angular-ui.min.css");
    	
    	$this->view->headScript()->appendFile("//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
    	$this->view->headScript()->appendFile("//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js");
    	$this->view->headScript()->appendFile("//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js");
    	$this->view->headScript()->appendFile("//ajax.googleapis.com/ajax/libs/angularjs/1.0.1/angular.min.js");
    	$this->view->headScript()->appendFile("https://raw.github.com/angular-ui/angular-ui/master/build/angular-ui.min.js");
    	
    	$this->view->headScript()->appendFile("/webapp/js/ui/twBootstrap.js");
    	$this->view->headScript()->appendFile("/webapp/js/profile/index.js");
    	 
    	 
		$this->view->headTitle('Home');
    }
}

