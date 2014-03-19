<?php

namespace EcampStoryboard;

use Zend\View\Model\ViewModel;

use EcampCore\Plugin\AbstractStrategy;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;
use EcampCore\Entity\Event;
use EcampCore\Entity\Plugin;

class Strategy extends AbstractStrategy
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getSectionRepo()
    {
        return $this->getServiceLocator()->get('EcampStoryboard\Repository\Section');
    }

    /**
     * @see \EcampCore\Plugin\AbstractStrategy::createViewModel()
     */
    public function createViewModel(EventPlugin $eventPlugin, Medium $medium)
    {
        $sections = $this->getSectionRepo()->findBy(array('eventPlugin' => $eventPlugin));

        $view = new ViewModel();
        $view->setVariable('sections', $sections);
        $view->setTemplate($this->getTemplate($medium));

        return $view;
    }

    private function getTemplate(Medium $medium)
    {
        return 'ecamp-storyboard/' . $medium->getName();
    }

    public function create(Event $event, Plugin $plugin)
    {
        return parent::create($event, $plugin);
    }

    public function delete(EventPlugin $eventPlugin)
    {
        $criteria = array('eventPlugin' => $eventPlugin);
        $sections = $this->getSectionRepo()->findBy($criteria);

        foreach ($sections as $section) {
            $this->remove($section);
        }

        parent::delete($eventPlugin);
    }

}
