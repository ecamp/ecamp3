<?php

namespace EcampCore\Render;

use EcampCore\Entity\Event;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPrototype;

use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class EventRenderer
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
	
	
	protected function getPluginRenderer(){
		$pluginRenderer = new PluginRenderer();
		$pluginRenderer->setServiceLocator($this->serviceLocator);
		
		return $pluginRenderer;
	}
	
	
	
	/**
	 * @param EcampCore\Entity\Event $event
	 * @param EcampCore\Entity\Medium $medium
	 * @return Zend\View\Model\ViewModel
	 */
	public function render(Event $event, Medium $medium = null){
		$medium = $medium ?: $this->repo()->mediumRepository()->getDefaultMedium();
		$eventTemplate = $this->getEventTemplate($event->getPrototype(), $medium);

		$view = new ViewModel();
		$view->setTemplate($eventTemplate->getFilename());
		$view->setVariable('event', $event);
		$view->setVariable('eventPrototype', $event->getPrototype());
		$view->setVariable('eventTemplate', $eventTemplate);
		
		
		$pluginInstances = array();
		
		foreach($event->getPluginInstances() as $pluginInstance){
			$pluginPrototypeId = $pluginInstance->getPluginPrototype()->getId();
			$pluginInstances[$pluginPrototypeId] = $pluginInstance;
		}
		
		foreach($eventTemplate->getPluginPositions() as $pluginPos){
			$pluginPrototypeId = $pluginPos->getPluginPrototype()->getId();
			$pluginInstance = $pluginInstances[$pluginPrototypeId];
			
			$pluginView = $this->getPluginRenderer()->render($pluginInstance, $medium);
			$view->addChild($pluginView, $pluginPos->getContainer());
		}
		
		return $view;
	}
	
	
	/**
	 * @param EventPrototype $eventPrototype
	 * @param Medium $medium
	 * @return EcampCore\Entity\EventTemplate
	 */
	private function getEventTemplate(EventPrototype $eventPrototype, Medium $medium){
		return 
			$this->repo()->eventTemplateRepository()->findOneBy(array(
				'medium' => $medium,
				'eventPrototype' => $eventPrototype
			));
	}
	
}
