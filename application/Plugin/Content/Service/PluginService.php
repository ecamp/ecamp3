<?php

namespace Plugin\Content\Service;


/**
 * @method CoreApi\Service\EventService Simulate
 */
class PluginService
{	

	/**
	 * @var CoreApi\Service\EventService
	 * @Inject CoreApi\Service\EventService
	 */
	private $eventService;
	
	
	public function save($params)
	{	
		$id = $params["id"];
		$text = $params["text"];
		
		$pluginInstance = $this->eventService->getPluginInstance($id);
		$content = $pluginInstance->getStrategyInstance()->getContent();
		
		$content->setText($text);
		
		$response = array();
		$response["text"] = $content->getText();
		
		return $response;
	}
	
}