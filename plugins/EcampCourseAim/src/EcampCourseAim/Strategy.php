<?php

namespace EcampCourseAim;

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
        return $this->getServiceLocator()->get('EcampCourseAim\Repository\Item');
    }


    /**
     * @see \EcampCore\Plugin\AbstractStrategy::createViewModel()
     */
    public function createViewModel(EventPlugin $eventPlugin, Medium $medium)
    {
        $urlHelper = $this->getServiceLocator()->get('ViewHelperManager')->get('url');

        $aim_roots = $this->getItemRepo()->findBy(
            array('camp' => $eventPlugin->getCamp(), 'parent' => null)
        );

        $aim_tree = array();

        foreach($aim_roots as $aim){

            $item = array(
                'text' => $aim->getText(),
                'id' => $aim->getId(),
                'selectable' => false,
                'nodes' => array()
            );

            foreach($aim->getChildren() as $child)
            {
                $item['nodes'][] = array(
                    'text' => $child->getText(),
                    'id' => $child->getId(),
                    'url' => $urlHelper('api-courseaim/items', array('eventPlugin' => $eventPlugin->getId(), 'item' => $child->getId())),
                    'state' => array(
                        'selected' => $child->isLinkedToEventPlugin($eventPlugin)
                    )
                );
            }

            $aim_tree[] = $item;
        }

        $view = new ViewModel();
        $view->setTemplate($this->getTemplate($medium));
        $view->setVariable('aim_tree', $aim_tree);

        return $view;
    }

    private function getTemplate(Medium $medium)
    {
        return $medium->getName();
    }

}
