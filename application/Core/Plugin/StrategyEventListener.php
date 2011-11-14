<?php
/*
 * Copyright (C) 2011 Urban Suppiger
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

namespace Core\Plugin;
 
use \Doctrine\ORM,
	\Doctrine\Common;
	
/**
 * The PluginStrategyEventListener will initialize a strategy after the
 * plugin itself was loaded.
 */
class StrategyEventListener implements \Doctrine\Common\EventSubscriber {

	protected $view;
	protected $em;

	public function __construct(\Zend_View_Interface $view, \Doctrine\ORM\EntityManager $em) {
		$this->view = $view;
		$this->em = $em;
	}

	public function getSubscribedEvents() {
	   return array(\Doctrine\ORM\Events::postLoad);
	}

	public function postLoad(\Doctrine\ORM\Event\LifecycleEventArgs $args) {
		$plugin= $args->getEntity();
		
		/* post load PluginStrategy into Plugin */
		if ($plugin instanceof \Core\Entity\Plugin) {
			$strategy  = $plugin->getStrategyClassName();
			$strategyInstance = new $strategy($this->em, $this->view, $plugin);
			
			/* load plugin */
			$strategyInstance->loadObjects( );
			//$strategyInstance->setView($this->view);
			
			$plugin->setStrategy($strategyInstance);
		}
	}
}