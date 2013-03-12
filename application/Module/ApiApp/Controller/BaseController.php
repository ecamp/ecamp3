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
 
namespace ApiApp\Controller;

use CoreApi\Entity\BaseEntity;

abstract class BaseController extends \Zend_Rest_Controller
{
	/**
	 * @var PhpDI\IKernel
	 * @Inject PhpDI\IKernel
	 */
	protected $kernel;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 * @Inject Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * @var CoreApi\Acl\ContextProvider
	 * @Inject CoreApi\Acl\ContextProvider
	 */
	protected $contextProvider;
	
	/**
	 * logged in user
	 * @var CoreApi\Entity\User
	 */
	protected $me;

	/**
	 * List of Serializers
	 * @var array
	 */
	private $serializers = array();
	
	public function init()
	{
		parent::init();
		
		\Zend_Registry::get('kernel')->Inject($this);
		
		$this->me = $this->contextProvider->getContext()->getMe();
		
		$this->_helper->viewRenderer->setNoRender(true);
		
		$this->getResponse()->setHeader('Content-Type', 'application/json');
		$this->getResponse()->setHeader('Access-Control-Allow-Origin', 'http://www.ecamp3.dev');
		$this->getResponse()->setHeader('Access-Control-Allow-Credentials', 'true');
		
	}
	
	
	protected function createFilter(){
		$args = func_get_args();
		$filter = array();
		
		foreach($args as $arg){
			$v = $this->getRequest()->getParam($arg);
			
			if($v != null){
				$filter[$arg] = $v;
			}
		}
		
		return $filter;
	}
	
	protected function getId(){
		return $this->getRequest()->getParam('id');
	}
	
	protected function getMime(){
		return $this->getRequest()->getParam('mime', 'json');
	}
	
	protected function defineSerializer($class, $serializer){
		$this->serializers[$class] = $serializer;
	}
	
	protected function getSerializer($class){
		$cm = $this->em->getClassMetadata($class);
		if($cm == null){
			throw new Exception("[$class] ist keine Entity-Klasse");
		}
		
		$class = $cm->name;
		if(array_key_exists($class, $this->serializers)){
			return $this->serializers[$class];
		}
		
		throw new Exception("Es existiert kein Serializer für [$class]");
	}
	
	protected function serialize($list){
		if(is_array($list)){
			$items = array();
			foreach($list as $k => $item){
				$serializer = $this->getSerializer(get_class($item));
				$items[$k] = $serializer($item);
			}
			return $items;
			
		} else {
			$serializer = $this->getSerializer(get_class($list));
			return $serializer($list);
		}
	}
	
	protected function setReturn(array $array){
		$body = null;
		
		switch($this->getMime()){
			case 'json':
				$body = json_encode($array, JSON_UNESCAPED_SLASHES);
				break;

			case 'xml':
				$body = $this->xml_encode($array);
				break;
		}
		
		$this->getResponse()->setBody($body);
	}
	
	protected function xml_encode($mixed, $domElement=null, $DOMDocument=null){
		if(is_null($DOMDocument)){
			$DOMDocument=new \DOMDocument;
			$DOMDocument->formatOutput=true;
			$this->xml_encode($mixed, $DOMDocument, $DOMDocument);
			echo $DOMDocument->saveXML();
		}
		else{
			if(is_array($mixed)){
				foreach($mixed as $index=>$mixedElement){
					if(is_int($index)){
						if($index==0){
							$node=$domElement;
						}
						else{
							$node=$DOMDocument->createElement($domElement->tagName);
							$domElement->parentNode->appendChild($node);
						}
					}
					else{
						$plural=$DOMDocument->createElement($index);
						$domElement->appendChild($plural);
						$node=$plural;
						if(rtrim($index,'s')!==$index){
							$singular=$DOMDocument->createElement(rtrim($index,'s'));
							$plural->appendChild($singular);
							$node=$singular;
						}
					}
					$this->xml_encode($mixedElement,$node,$DOMDocument);
				}
			}
			else{
				$domElement->appendChild($DOMDocument->createTextNode($mixed));
			}
		}
	}
		
}