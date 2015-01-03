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
        $item = $this->repo->find($id);

        if (!$item) {
            throw new DomainException('Data not found', 404);
        }

        return new MaterialItemDetailResource($item);
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

        $item = $this->repo->find($id);
        if (!$item) {
            throw new DomainException('Data not found', 404);
        }

        $item->setQuantity($data->quantity);
        $item->setText($data->text);

        /* update lists based on array */

        /* Step 1: remove lists that are not in array anymore */
        foreach ($item->getLists() as $list) {
            if (!in_array($list->getId(), $data->lists)) {

                $item->removeList($list);
            }
        }

        /* Step 2: add additional lists from array */
        foreach ($data->lists as $id) {
            $list = $this->listRepo->find($id);

            if ($list && !$item->getLists()->contains($list) ) {

                $item->addList($list);
            }
        }

        return new MaterialItemDetailResource($item);
    }
}
