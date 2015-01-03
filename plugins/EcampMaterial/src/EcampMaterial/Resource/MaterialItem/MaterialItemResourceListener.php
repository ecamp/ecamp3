<?php
namespace EcampMaterial\Resource\MaterialItem;

use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class MaterialItemResourceListener extends AbstractListenerAggregate
{
    protected $repo;
    protected $listRepo;

    public function __construct($repo, $listRepo)
    {
        $this->repo = $repo;
        $this->listRepo = $listRepo;
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

        if (!$entity) {
            throw new DomainException('Data not found', 404);
        }

        return new MaterialItemDetailResource($entity);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['eventPlugin'] = $e->getRouteParam('eventPlugin', $e->getQueryParam('eventPlugin'));

        return $this->repo->getCollection($params);
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');

        $entity = $this->repo->find($id);
        if (!$entity) {
            throw new DomainException('Data not found', 404);
        }

        $entity->setQuantity($data->quantity);
        $entity->setText($data->text);

        /* update lists based on array */
        $lists = $entity->getLists();
        foreach ($lists as $list) {
            if (!in_array($list->getId(), $data->listsIdArray)) {
                $listEntity = $this->listRepo->find($list);

                $lists->removeElement($listEntity);

                $listEntity->getItems()->removeElement($entity);
            }
        }

        foreach ($data->listsIdArray as $id) {
            $list = $this->listRepo->find($id);

            if ( ! $lists->contains($list) ) {

                $lists->add($list);
            }
        }

        return new MaterialItemDetailResource($entity);
    }
}
