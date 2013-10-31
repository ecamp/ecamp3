<?php

namespace EcampApi\Resource\Search;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class UserCollectionRenderingListener extends AbstractListenerAggregate
{
    public $group = null;
    public $camp = null;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->getSharedManager()->attach(
            'PhlyRestfully\Plugin\HalLinks',
            'renderCollection.resource',
            array($this, 'renderCollectionResource'),
            200
        );
    }

    public function renderCollectionResource($e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof \EcampCore\Entity\User) {
            $params['resource'] = new \EcampApi\Resource\Search\UserResource($resource, $this->group, $this->camp);

            return;
        }

    }

}
