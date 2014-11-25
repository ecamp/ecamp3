<?php

namespace EcampTextarea\Resource;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampTextarea\Entity\Textarea;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Paginator;

class TextareaResourceListener extends AbstractListenerAggregate
{
    /**
     * @var \Doctrine\Orm\EntityManager
     */
    protected $em;

    /**
     * @var \EcampCore\Repository\EventPluginRepository
     */
    protected $eventPluginRepo;

    /**
     * @var \EcampTextarea\Repository\TextareaRepository
     */
    protected $textareaRepo;

    public function __construct($em, $eventPluginRepo, $textareaRepo)
    {
        $this->em = $em;
        $this->eventPluginRepo = $eventPluginRepo;
        $this->textareaRepo = $textareaRepo;
    }


    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->textareaRepo->find($id);

        if(!$entity){
            throw new DomainException('Textarea not found', 404);
        }

        return new TextareaResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPluginId');

        $q = $this->textareaRepo->createQueryBuilder('t');
        $q->where('t.eventPlugin = :eventPluginId');
        $q->setParameter('eventPluginId', $eventPluginId);

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->textareaRepo->find($id);

        if(isset($data->text)) {
            $entity->setText($data->text);
        }

        return new TextareaResource($entity);
    }

}