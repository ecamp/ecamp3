<?php

namespace EcampMaterial;

use Zend\View\Model\ViewModel;

use EcampCore\Plugin\AbstractStrategy;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;
use EcampCore\Entity\Plugin;

class Strategy extends AbstractStrategy
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getItemRepo()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Repository\MaterialItem');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getListRepo()
    {
        return $this->getServiceLocator()->get('EcampMaterial\Repository\MaterialList');
    }

    /**
     * @see \EcampCore\Plugin\AbstractStrategy::createViewModel()
     */
    public function createViewModel(EventPlugin $eventPlugin, Medium $medium)
    {
        $items = $this->getItemRepo()->findBy(array('eventPlugin' => $eventPlugin));
        $lists = $this->getListRepo()->findBy(array('camp' => $eventPlugin->getCamp()));

        $urlHelper = $this->getServiceLocator()->get('ControllerPluginManager')->get('url');

        $forms = array();

        foreach ($items as $item) {
            $form = new Form\MaterialItemForm($this->getFormElementManager());
            $form->bind($item);

            $form->setAction(
                    $urlHelper->fromRoute(
                            'plugin/material/default',
                            array('eventPluginId' => $item->getEventPlugin()->getId(), 'controller' => 'item', 'action' => 'save', 'id' => $item->getId())
                    )
            );

            $forms[$item->getId()] = $form;
        }

        $newform = new Form\MaterialItemForm($this->getFormElementManager());
        $newform->setAction(
                $urlHelper->fromRoute(
                        'plugin/material/default',
                        array('eventPluginId' => $item->getEventPlugin()->getId(), 'controller' => 'item', 'action' => 'create')
                )
        );

        $view = new ViewModel();
        $view->setVariable('items', $items);
        $view->setVariable('lists', $lists);
        $view->setVariable('forms', $forms);
        $view->setVariable('newform', $newform);
        $view->setTemplate($this->getTemplate($medium));

        return $view;
    }

    private function getTemplate(Medium $medium)
    {
        return 'ecamp-material/' . $medium->getName();
    }

}
