<?php

namespace EcampCore\Render;

use Zend\View\Model\ViewModel;

use EcampCore\Entity\Medium;
use EcampCore\Entity\PluginInstance;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class PluginRenderer
	implements ServiceLocatorAwareInterface
{
	
	private $serviceLocator;
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator){
		$this->serviceLocator = $serviceLocator;
	}
	
	public function getServiceLocator(){
		return $this->serviceLocator;
	}
	
	
	/**
	 * @var EcampCore\RepositoryUtil\RepositoryProvider
	 */
	private $repo;
	
	/**
	 * @return EcampCore\RepositoryUtil\RepositoryProvider
	 */
	public function repo(){
		if($this->repo == null){
			$this->repo = $this->serviceLocator->get('ecamp.repositoryutil.provider');
		}
		return $this->repo;
	}
	
	
	/**
	 * @param PluginInstance $pluginInstance
	 * @param Medium $medium
	 * @return Zend\View\Model\ViewModel
	 */
	public function render(PluginInstance $pluginInstance, Medium $medium = null){
		$medium = $medium ?: $this->repo()->mediumRepository()->getDefualtMedium();
		$view = $pluginInstance->getStrategyInstance()->render($medium);
		
		if($view instanceof ViewModel){
			return $view;
		} else {
			return new ViewModel($view);
		}
	}
}
