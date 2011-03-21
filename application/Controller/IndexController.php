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

class IndexController extends \Controller\BaseController
{

    public function init()
    {
	    Zend_Layout::getMvcInstance()->setLayout('layout');

	    parent::init();
    }

    public function indexAction()
    {
		$this->view->headTitle('Home');
    }
	
	public function createpluginAction()
	{
		$plugin = new \Entity\Plugin();		
		$headerstrategy = new \Plugin\HeaderStrategy($this->em, $plugin);
		
		$plugin->setStrategy($headerstrategy);
		
		$this->em->persist($plugin);
		$headerstrategy->persist();
		
		$this->em->flush();
		exit();
	}
	
	public function loadpluginAction()
	{
		/* move this to bootsrap */
		$event = new \Plugin\StrategyEventListener($this->view, $this->em);
		$this->em->getEventManager()->addEventSubscriber($event);

		$id = $this->getRequest()->getParam("id");
		$plugin = $this->em->getRepository("Entity\Plugin")->find($id);
		
		echo $plugin->getStrategyInstance()->renderFrontend(); exit();
	}
}

