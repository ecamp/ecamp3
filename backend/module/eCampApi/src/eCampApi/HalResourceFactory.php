<?php

namespace eCampApi;

use Laminas\ApiTools\Hal\Collection;
use Laminas\ApiTools\Hal\Entity;
use Laminas\ApiTools\Hal\Metadata\Metadata;
use Laminas\ApiTools\Hal\ResourceFactory;
use Laminas\Paginator\Paginator;
use Traversable;

class HalResourceFactory extends ResourceFactory {
    public function createEntityFromMetadata($object, Metadata $metadata, $renderEmbeddedEntities = true) {
        $halEntity = parent::createEntityFromMetadata($object, $metadata, $renderEmbeddedEntities);

        if ($halEntity instanceof Entity) {
            $entity = $halEntity->getEntity();
            if (!isset($entity->_hydrateInfo_)) {
                $hydrator = $metadata->getHydrator();

                if (method_exists($hydrator, 'HydrateInfo')) {
                    $entity->_hydrateInfo_ = call_user_func([$hydrator, 'HydrateInfo']);
                }
            }
        }

        return $halEntity;
    }

    /**
     * @param array|Paginator|Traversable $object
     */
    public function createCollectionFromMetadata($object, Metadata $metadata): Collection {
        $halCollection = parent::createCollectionFromMetadata($object, $metadata);

        $collection = $halCollection->getCollection();
        $entityRoute = $halCollection->getEntityRoute();
        $entityRouteParams = $halCollection->getEntityRouteParams();
        $entityRouteOptions = $halCollection->getEntityRouteOptions();
        $halCollection = new HalCollection($collection, $entityRoute, $entityRouteParams, $entityRouteOptions);

        if (isset($object->_hydrateInfo_)) {
            $halCollection->_hydrateInfo_ = $object->_hydrateInfo_;
        } else {
            $hydrator = $metadata->getHydrator();
            if (method_exists($hydrator, 'HydrateInfo')) {
                $halCollection->_hydrateInfo_ = call_user_func([$hydrator, 'HydrateInfo']);
            }
        }

        return $halCollection;
    }
}
