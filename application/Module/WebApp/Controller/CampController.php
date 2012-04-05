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


class WebApp_CampController extends \WebApp\Controller\BaseController
{
	
	public function init()
    {
	    parent::init();

        if(!isset($this->me))
		{
			$this->_forward("index", "login");
			return;
		}
		
		/* load context */
		$context = $this->contextProvider->getContext();
	    $this->view->camp = $context->getCamp();
	    $this->view->group = $context->getGroup();
	    $this->view->owner = $context->getUser();

	    $this->setNavigation(new \WebApp\Navigation\Camp($context->getCamp()));
		
	}

	
	public function showAction()
	{}
	
}

