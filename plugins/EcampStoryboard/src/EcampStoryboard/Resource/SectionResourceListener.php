<?php
namespace EcampStoryboard\Resource;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use EcampStoryboard\Entity\Section;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Paginator;

class SectionResourceListener extends AbstractListenerAggregate
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
     * @var \EcampStoryboard\Repository\SectionRepository
     */
    protected $sectionRepo;

    public function __construct($em, $eventPluginRepo, $sectionRepo)
    {
        $this->em = $em;
        $this->eventPluginRepo = $eventPluginRepo;
        $this->sectionRepo = $sectionRepo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('create', array($this, 'onCreate'));
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));
        $this->listeners[] = $events->attach('delete', array($this, 'onDelete'));
    }

    public function onCreate(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPluginId');
        $eventPlugin = $this->eventPluginRepo->find($eventPluginId);

        $data = $e->getParam('data');

        $q = $this->sectionRepo->createQueryBuilder('s');
        $q->select('max(s.position)');
        $q->where('s.eventPlugin = :eventPluginId');
        $q->setParameter('eventPluginId', $eventPluginId);
        $maxPos = $q->getQuery()->getSingleScalarResult();

        $section = new Section($eventPlugin);
        $section->setDurationInMinutes($data->duration);
        $section->setText($data->text);
        $section->setInfo($data->info);
        $section->setPosition($maxPos + 1);

        $this->em->persist($section);

        return new SectionResource($section);
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->sectionRepo->find($id);

        if (!$entity) {
            throw new DomainException('Section not found', 404);
        }

        return new SectionResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $eventPluginId = $e->getRouteParam('eventPluginId');

        $q = $this->sectionRepo->createQueryBuilder('s');
        $q->where('s.eventPlugin = :eventPluginId');
        $q->orderBy('s.position', 'ASC');
        $q->setParameter('eventPluginId', $eventPluginId);

        return new Paginator(new PaginatorAdapter(new ORMPaginator($q->getQuery())));
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->sectionRepo->find($id);

        if (isset($data->duration)) {
            $entity->setDurationInMinutes($data->duration);
        }

        if (isset($data->text)) {
            $entity->setText($data->text);
        }

        if (isset($data->info)) {
            $entity->setInfo($data->info);
        }

        if (isset($data->position)) {
            $entity->setPosition($data->position);
        }

        return new SectionResource($entity);
    }

    public function onDelete(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $entity = $this->sectionRepo->find($id);

        $this->em->remove($entity);

        return true;
    }

}
