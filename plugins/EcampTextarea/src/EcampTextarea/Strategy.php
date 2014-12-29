<?php

namespace EcampTextarea;

use EcampCore\Entity\Event;
use EcampCore\Entity\Plugin;
use EcampTextarea\Entity\Textarea;
use Zend\View\Model\ViewModel;

use EcampCore\Plugin\AbstractStrategy;
use EcampCore\Entity\Medium;
use EcampCore\Entity\EventPlugin;

class Strategy extends AbstractStrategy
{
    /**
     * @return \EcampTextarea\Repository\TextareaRepository
     */
    protected function getTextareaRepo()
    {
        return $this->getServiceLocator()->get('EcampTextarea\Repository\Textarea');
    }


    public function create(Event $event, Plugin $plugin){
        $eventPlugin = parent::create($event, $plugin);

        $textarea = new Textarea($eventPlugin);
        $this->persist($textarea);

        return $eventPlugin;
    }

    /**
     * @see \EcampCore\Plugin\AbstractStrategy::createViewModel()
     */
    public function createViewModel(EventPlugin $eventPlugin, Medium $medium)
    {
        $textarea = $this->getTextareaRepo()->findOneBy(
            array('eventPlugin' => $eventPlugin)
        );

        $view = new ViewModel();
        $view->setVariable('textarea', $textarea);
        $view->setTemplate($this->getTemplate($medium));

        return $view;
    }

    private function getTemplate(Medium $medium)
    {
        return 'ecamp-textarea/' . $medium->getName();
    }

}
