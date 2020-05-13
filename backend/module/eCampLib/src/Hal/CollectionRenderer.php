<?php

namespace eCamp\Lib\Hal;

use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Hydrator\Resolver\BaseResolver;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Link\LinkCollection;
use Laminas\ApiTools\Hal\Metadata\Metadata;
use Laminas\ApiTools\Hal\Plugin\Hal;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManagerInterface;

class CollectionRenderer extends AbstractListenerAggregate {
    /**
     * @var callable[]
     */
    protected $sharedListeners = [];

    public function attach(EventManagerInterface $events, $priority = 1) {
        $sharedEvents = $events->getSharedManager();
        $this->attachShared($sharedEvents);
    }

    public function detach(EventManagerInterface $events) {
        parent::detach($events);

        $sharedEvents = $events->getSharedManager();
        $this->detachShared($sharedEvents);
    }

    public function attachShared(SharedEventManagerInterface $sharedEvents) {
        $this->sharedListeners[] = $sharedEvents->attach('Laminas\ApiTools\Hal\Plugin\Hal', 'renderEntity', [$this, 'renderEntity'], 100);
        $this->sharedListeners[] = $sharedEvents->attach('Laminas\ApiTools\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'renderEntityPost'], 100);
        $this->sharedListeners[] = $sharedEvents->attach('Laminas\ApiTools\Hal\Plugin\Hal', 'renderCollection', [$this, 'renderCollection'], 100);
    }

    public function detachShared(SharedEventManagerInterface $sharedEvents) {
        foreach ($this->sharedListeners as $index => $callback) {
            $sharedEvents->detach($callback);
            unset($this->sharedListeners[$index]);
        }
    }

    public function renderEntity(Event $e) {
        /** @var Hal $hal */
        $hal = $e->getTarget();

        /** @var Entity $halEntity */
        $halEntity = $e->getParam('entity');
        $entity = $halEntity->getEntity();

        if ($entity instanceof EntityLink) {
            $entity = $entity->getEntity();

            /** @var Metadata $metadata */
            $metadata = $hal->getMetadataMap()->get($entity);
            $route = $metadata->getRoute();
            $routeIdentifier = $metadata->getRouteIdentifierName();

            $halEntity->setLinks(new LinkCollection());
            $hal->injectSelfLink($halEntity, $route, $routeIdentifier);
        }

        if (isset($entity->_hydrateInfo_)) {
            foreach ($entity->_hydrateInfo_ as $item) {
                /** @var BaseResolver $item */
                $links = $item->getLinks($entity);
                foreach ($links as $link) {
                    $halEntity->getLinks()->add($link);
                }
            }
        }
    }

    public function renderEntityPost(Event $e) {
        /** @var Hal $hal */
        $hal = $e->getTarget();

        /** @var Entity $halEntity */
        $halEntity = $e->getParam('entity');
        /** @var \ArrayObject $payload */
        $payload = $e->getParam('payload');

        //  V1: ID = URL
        // ==============
//        $self = $halEntity->getLinks()->get('self');
//        $selfLink = $hal->fromLink($self);
//
//        $payload->offsetSet('id', $selfLink['href']);
//        if ($halEntity->getEntity() instanceof EntityLink) {
//            $payload->offsetUnset('_links');
//        }

        //  V2: Proxy ohne ID
        // ===================
        if ($halEntity->getEntity() instanceof EntityLink) {
            $payload->offsetUnset('id');
        }

        //  V3: Proxy declaration
        // =======================
//        $payload->exchangeArray(
//            [ 'proxy' => ($halEntity->getEntity() instanceof EntityLink) ] + $payload->getArrayCopy()
//        );
    }

    public function renderCollection(Event $e) {
        /** @var Hal $hal */
        $hal = $e->getTarget();
        $halCollection = $e->getParam('collection');
    }
}
