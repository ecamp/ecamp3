<?php

namespace EcampStoryboard;

use Zend\View\Model\ViewModel;

use EcampCore\Plugin\AbstractStrategy;

class Strategy extends AbstractStrategy
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getSectionRepo()
    {
        return $this->getServiceLocator()->get('EcampStoryboard\Repository\Section');
    }

    public function getTitle()
    {
        return $this->getEventPlugin()->getInstanceName();
    }

    /**
     * @see \EcampCore\Plugin\AbstractStrategy::createViewModel()
     */
    public function createViewModel()
    {
        $sections = $this->getSectionRepo()->findBy(array('eventPlugin' => $this->getEventPlugin()));

        $view = new ViewModel();
        $view->setVariable('sections', $sections);
        $view->setTemplate($this->getTemplate());

        return $view;
    }

    private function getTemplate()
    {
        return 'ecamp-storyboard/' . $this->getMedium()->getName();
    }

}
