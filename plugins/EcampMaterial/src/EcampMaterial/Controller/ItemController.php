<?php

namespace EcampMaterial\Controller;

use Zend\Http\Response;

use Zend\Form\FormInterface;

use EcampLib\Validation\ValidationException;

use EcampMaterial\Form\MaterialItemForm;

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

        $form = new MaterialItemForm($this->getFormElementManager());

        $form->setAction(
                $this->url()->fromRoute(
                        'plugin/material/default',
                        array('eventPluginId' => $eventPlugin->getId(), 'controller' => 'item', 'action' => 'create')
                )
        );

        $form->setData($this->params()->fromPost());

        if ($form->isValid()) {

            $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

            try {
                $item = $this->getItemService()->create($eventPlugin, $data);

                $form->setAction(
                        $this->url()->fromRoute(
                                'plugin/material/default',
                                array('eventPluginId' => $item->getEventPlugin()->getId(), 'controller' => 'item', 'action' => 'save', 'id' => $item->getId())
                        )
                );
                $form->bind($item);

                $forms = array();
                $forms[$item->getId()] = $form;

                $viewModel = new ViewModel();
                $viewModel->setVariable('item', $item);
                $viewModel->setVariable('forms', $forms);
                $viewModel->setVariable('eventPlugin', $item->getEventPlugin());
                $viewModel->setTemplate('ecamp-material/item');

                return $viewModel;

            } catch (ValidationException $ex) {
                $form->setMessages($ex->getValidationMessages());
            }
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_422);

            $viewModel = new ViewModel();
            $viewModel->setVariable('newform', $form);
            $viewModel->setVariable('eventPlugin', $eventPlugin);
            $viewModel->setTemplate('ecamp-material/item-newform');

            return $viewModel;
        }

    }

    public function saveAction()
    {
        $itemId = $this->params()->fromRoute('id');
        $item = $this->getItemRepo()->find($itemId);

        $form = new MaterialItemForm($this->getFormElementManager());
        $form->bind($item);

        $form->setAction(
                $this->url()->fromRoute(
                        'plugin/material/default',
                        array('eventPluginId' => $item->getEventPlugin()->getId(), 'controller' => 'item', 'action' => 'save', 'id' => $item->getId())
                )
        );

        $form->setData($this->params()->fromPost());

        if ($form->isValid()) {

            $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

            try {
                $this->getItemService()->update($item, $data);
                $form->bind($item);

            } catch (ValidationException $ex) {
                $form->setMessages($ex->getValidationMessages());
            }
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_422);
        }

        $forms = array();
        $forms[$item->getId()] = $form;

        $viewModel = new ViewModel();
        $viewModel->setVariable('item', $item);
        $viewModel->setVariable('forms', $forms);
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
