<?php

namespace EcampMaterial\Listener;

use EcampMaterial\Resource\MaterialItem\MaterialItemBriefResource;
use EcampMaterial\Entity\MaterialItem;

use EcampApi\Listener\CollectionRenderingListener as ApiCollectionRenderingListener;

use Zend\EventManager\SharedListenerAggregateInterface;

class CollectionRenderingListener extends ApiCollectionRenderingListener
    implements SharedListenerAggregateInterface
{

    public function renderCollectionResource(\Zend\EventManager\Event $e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof MaterialItem) {
            $params['resource']    = new MaterialItemBriefResource($resource);

            return;
        }

    }

}
