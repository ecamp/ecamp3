<?php

namespace EcampApi\Listener;

use EcampApi\Resource\Camp\CampBriefResource;
use EcampApi\Resource\Collaboration\CollaborationBriefResource;
use EcampApi\Resource\Collaboration\CollaborationDetailResource;
use EcampApi\Resource\Day\DayBriefResource;
use EcampApi\Resource\Event\EventBriefResource;
use EcampApi\Resource\EventCategory\EventCategoryBriefResource;
use EcampApi\Resource\EventInstance\EventInstanceBriefResource;
use EcampApi\Resource\EventResp\EventRespBriefResource;
use EcampApi\Resource\Group\GroupBriefResource;
use EcampApi\Resource\Membership\MembershipBriefResource;
use EcampApi\Resource\Period\PeriodBriefResource;
use EcampApi\Resource\User\UserBriefResource;
use EcampCore\Entity\Camp;
use EcampCore\Entity\CampCollaboration;
use EcampCore\Entity\Day;
use EcampCore\Entity\Event;
use EcampCore\Entity\EventCategory;
use EcampCore\Entity\EventInstance;
use EcampCore\Entity\EventResp;
use EcampCore\Entity\Group;
use EcampCore\Entity\GroupMembership;
use EcampCore\Entity\Period;
use EcampCore\Entity\User;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Paginator\Paginator;

class CollectionRenderingListener implements SharedListenerAggregateInterface
{
    protected $renderCollectionResourceListener;
    protected $renderCollectionListener;
    protected $getListPreListener;

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        if($events->detach('PhlyRestfully\Plugin\HalLinks', $this->renderCollectionResourceListener));
        if($events->detach('PhlyRestfully\Plugin\HalLinks', $this->renderCollectionListener));
        if($events->detach('PhlyRestfully\ResourceController', $this->getListPreListener));
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->renderCollectionResourceListener = $events->attach(
            'PhlyRestfully\Plugin\HalLinks',
            'renderCollection.resource',
            array($this, 'renderCollectionResource'),
            100
        );

        $this->renderCollectionListener = $events->attach(
            'PhlyRestfully\Plugin\HalLinks',
            'renderCollection',
            array($this, 'renderCollection'),
            100
        );

        $this->getListPreListener = $events->attach(
            'PhlyRestfully\ResourceController',
            'getList.pre',
            array($this, 'getListPre'),
            100
        );
    }


    public function renderCollectionResource(\Zend\EventManager\Event $e)
    {
        $params = $e->getParams();
        $resource = $params['resource'];

        if ($resource instanceof Camp) {
            $params['resource'] = new CampBriefResource($resource);

            return;
        }

        if ($resource instanceof User) {
            $params['resource'] = new UserBriefResource($resource);

            return;
        }

        if ($resource instanceof CampCollaboration) {
            $params['resource'] = new CollaborationBriefResource($resource->getId(), $resource);

            return;
        }

        if ($resource instanceof Period) {
            $params['resource'] = new PeriodBriefResource($resource);

            return;
        }

        if ($resource instanceof Day) {
            $params['resource'] = new DayBriefResource($resource);

            return;
        }

        if ($resource instanceof Event) {
            $params['resource'] = new EventBriefResource($resource);

            return;
        }

        if ($resource instanceof EventInstance) {
            $params['resource'] = new EventInstanceBriefResource($resource);

            return;
        }

        if ($resource instanceof EventResp) {
            $params['resource'] = new EventRespBriefResource($resource);

            return;
        }

        if ($resource instanceof EventCategory) {
            $params['resource'] = new EventCategoryBriefResource($resource);

            return;
        }

        if ($resource instanceof Group) {
            $params['resource'] = new GroupBriefResource($resource);

            return;
        }

        if ($resource instanceof GroupMembership) {
            $params['resource'] = new MembershipBriefResource($resource->getId(), $resource);

            return;
        }

    }

    public function renderCollection(\Zend\EventManager\Event $e)
    {
        $collection = $e->getParam('collection');
        $paginator = $collection->collection;

        if (!$paginator instanceof Paginator) {
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

    public function getListPre(\Zend\EventManager\Event $e)
    {
        /** @var \PhlyRestfully\ResourceController $controller */
        $controller = $e->getTarget();

        /** @var \PhlyRestfully\Resource $resource */
        $resource = $controller->getResource();

        /** @var \Zend\Stdlib\Parameters $params */
        $params = $resource->getQueryParams();

        if ($params != null) {
            $limit = $params->get('limit', 'all');
            if ($limit == "all") {
                $params->set('limit', PHP_INT_MAX);
            }
        }
    }

}
