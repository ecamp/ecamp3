<?php

namespace eCamp\Web\View;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ViewModelTerminator extends AbstractListenerAggregate
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError'], -50);
    }

    public function onDispatchError(MvcEvent $e)
    {
        $res = $e->getResult();
        if ($res instanceof ViewModel) {
            $tpl = $res->getTemplate();
            if (substr($tpl, 0, 6) == 'e-camp') {
                $res->setTerminal(true);
            }
        }
    }
}
