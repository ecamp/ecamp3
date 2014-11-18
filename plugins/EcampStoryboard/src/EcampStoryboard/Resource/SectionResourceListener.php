<?php
namespace EcampStoryboard\Resource;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Paginator;

class SectionResourceListener extends AbstractListenerAggregate
{
    /**
     * @var \EcampStoryboard\Repository\SectionRepository
     */
    protected $repo;

    public function __construct($repo){
        $this->repo = $repo;
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
        $entity = $this->repo->find($id);

        if(!$entity){
            throw new DomainException('Section not found', 404);
        }

        return new SectionResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPluginId');

        $q = $this->repo->createQueryBuilder('s');
        $q->where('s.eventPlugin = :eventPluginId');
        $q->setParameter('eventPluginId', $eventPluginId);

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->repo->find($id);

        $entity->setDurationInMinutes($data->duration);
        $entity->setText($data->text);
        $entity->setInfo($data->info);

        return new SectionResource($entity);
    }

}