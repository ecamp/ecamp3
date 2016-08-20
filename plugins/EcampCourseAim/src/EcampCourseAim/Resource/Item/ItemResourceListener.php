<?php
namespace EcampCourseAim\Resource\Item;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class ItemResourceListener extends AbstractListenerAggregate
{
    protected $repo;
    protected $itemRepo;

    public function __construct($repo, $eventPluginRepo, $em)
    {
        $this->repo = $repo;
        $this->eventPluginRepo = $eventPluginRepo;
        $this->em = $em;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('delete', array($this, 'onDelete'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
    }

    public function onUpdate(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPlugin', $e->getQueryParam('eventPlugin'));
        $itemId = $e->getParam('id');

        $item = $this->repo->find($itemId);
        $eventPlugin = $this->eventPluginRepo->find($eventPluginId);

        $item->addEventPlugin($eventPlugin);

        $this->em->flush();

        return array('id' => $itemId );
    }

    public function onDelete(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPlugin', $e->getQueryParam('eventPlugin'));
        $itemId = $e->getParam('id');

        $item = $this->repo->find($itemId);
        $eventPlugin = $this->eventPluginRepo->find($eventPluginId);

        foreach($item->eventPlugins as $eventPlugin){
            print_r($eventPlugin->getId());
        }

        $item->removeEventPlugin($eventPlugin);

        foreach($item->eventPlugins as $eventPlugin){
            print_r($eventPlugin->getId());
        }

        $this->em->flush();

        return true;
    }

}
