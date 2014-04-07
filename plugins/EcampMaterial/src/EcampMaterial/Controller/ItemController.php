<?php

namespace EcampMaterial\Controller;

use EcampCore\Controller\AbstractEventPluginController;

use Zend\View\Model\ViewModel;

class ItemController extends AbstractEventPluginController
{

    /**
     * @return \EcampMaterial\Service\ItemService
     */
    private function getItemService()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Service\Item');
    }
    
    /**
     * @return \EcampMaterial\Repository\MaterialItemRepository
     */
    private function getItemRepo()
    {
    	return $this->getServiceLocator()->get('EcampMaterial\Repository\MaterialItem');
    }
    

    public function createAction()
    {
        $eventPlugin = $this->getRouteEventPlugin();
        $data = $this->params()->fromPost();

        $item = $this->getItemService()->create($eventPlugin, $data);

        $viewModel = new ViewModel();
        $viewModel->setVariable('item', $item);
        $viewModel->setVariable('eventPlugin', $eventPlugin);
        $viewModel->setTemplate('ecamp-material/item');

        return $viewModel;
    }
    
    public function saveAction()
    {
    	$itemId = $this->params()->fromRoute('id');
    	$item = $this->getItemRepo()->find($itemId);
    
    	$data = $this->params()->fromPost();
    
    	$this->getItemService()->update($item, $data);
    
    	$viewModel = new ViewModel();
    	$viewModel->setVariable('item', $item);
    	$viewModel->setVariable('eventPlugin', $item->getEventPlugin());
    	$viewModel->setTemplate('ecamp-material/item');
    
    	return $viewModel;
    }
    
    public function deleteAction()
    {
    	$itemId = $this->params()->fromRoute('id');
    	$item = $this->getItemRepo()->find($itemId);
    
    	try {
    		$this->getItemService()->delete($item);
    
    		$response = $this->getResponse();
    		$response->setStatusCode(Response::STATUS_CODE_200);
    
    		return $response;
    
    	} catch (\Exception $ex) {
    		$response = $this->getResponse();
    		$response->setStatusCode(Response::STATUS_CODE_500);
    		$response->setContent($ex->getMessage());
    
    		return $response;
    	}
    
    }

}
