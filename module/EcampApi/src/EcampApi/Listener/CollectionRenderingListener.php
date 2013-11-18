<?php

namespace EcampApi\Listener;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;

class CollectionRenderingListener implements SharedListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'PhlyRestfully\Plugin\HalLinks',
            'renderCollection.resource',
            array($this, 'renderCollectionResource'),
            100
        );

        $this->listeners[] = $events->attach(
            'PhlyRestfully\Plugin\HalLinks',
            array('renderResource', 'renderCollection'),
            array($this, 'renderCollection'),
            100
        );
    }

    public function renderCollectionResource($e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof \EcampCore\Entity\Camp) {
            $params['resource']    = new \EcampApi\Resource\Camp\CampBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\User) {
            $params['resource']    = new \EcampApi\Resource\User\UserBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\CampCollaboration) {
            $params['resource']    = new \EcampApi\Resource\Collaboration\CollaborationDetailResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\Period) {
            $params['resource']    = new \EcampApi\Resource\Period\PeriodBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\Day) {
            $params['resource']    = new \EcampApi\Resource\Day\DayBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\Event) {
            $params['resource']    = new \EcampApi\Resource\Event\EventBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\EventInstance) {
            $params['resource']    = new \EcampApi\Resource\EventInstance\EventInstanceBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\EventResp) {
            $params['resource']    = new \EcampApi\Resource\EventResp\EventRespBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\EventCategory) {
            $params['resource']    = new \EcampApi\Resource\EventCategory\EventCategoryBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\Group) {
            $params['resource']    = new \EcampApi\Resource\Group\GroupBriefResource($resource);

            return;
        }

        if ($resource instanceof \EcampCore\Entity\GroupMembership) {
            $params['resource']    = new \EcampApi\Resource\Membership\MembershipDetailResource($resource);

            return;
        }

    }

    public function renderCollection($e)
    {
        $collection = $e->getParam('collection');
        $paginator = $collection->collection;

        if (!$paginator instanceof \Zend\Paginator\Paginator) {
            return;
        }

        /* page number and size is not yet set by phplyrestfully */
        $paginator->setItemCountPerPage($collection->pageSize);

        $page = min(array($collection->page, $paginator->count()));
        $page = max(array(1, $page));
        $collection->setPage($page);
        $paginator->setCurrentPageNumber($page);

        $collection->setAttributes(array(
            'page'   => $paginator->getCurrentPageNumber(),
            'limit'  => $paginator->getItemCountPerPage(),
            'pages'	 => $paginator->count(),
            'count'	 => $paginator->getTotalItemCount()
        ));
    }

}
