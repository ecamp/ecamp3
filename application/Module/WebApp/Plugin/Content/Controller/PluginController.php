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

class Content_PluginController extends \WebApp\Controller\BasePluginController
{

    public function init()
    {
		parent::init();
		
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$id = $this->getRequest()->getParam("id");
		$this->plugin = $this->em->getRepository("Core\Entity\Plugin")->find($id);
		
		$this->getResponse()->setHeader('Content-Type', 'text/plain');
    }

    public function saveAction()
    {
		$content = $this->em->getRepository("Core\Plugin\Content\Entity\Content")->findOneBy(array('plugin' => $this->plugin->getId()));
		
		$response = array();
		$response['message'] = "I am a content plugin.\n\nOld value: ".$content->getText()."!\nNew value: ".$this->getRequest()->getParam("text")."!";
		
		$content->setText($this->getRequest()->getParam("text"));
		$this->em->flush();
		
		$this->getResponse()->setBody( Zend_Json::encode($response) );
    }
}

