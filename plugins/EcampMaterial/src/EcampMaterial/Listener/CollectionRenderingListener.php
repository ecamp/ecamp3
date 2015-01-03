<?php

namespace EcampMaterial\Listener;

use EcampMaterial\Resource\MaterialList\MaterialListBriefResource;

use EcampMaterial\Entity\MaterialList;

use EcampMaterial\Entity\MaterialItem;

use EcampApi\Listener\CollectionRenderingListener as ApiCollectionRenderingListener;

use Zend\EventManager\SharedListenerAggregateInterface;
use EcampMaterial\Resource\MaterialItem\MaterialItemDetailResource;

class CollectionRenderingListener extends ApiCollectionRenderingListener
    implements SharedListenerAggregateInterface
{

    public function renderCollectionResource(\Zend\EventManager\Event $e)
    {
        $resource = $e->getParam('resource');
        $params = $e->getParams();

        if ($resource instanceof MaterialItem) {
            $params['resource']    = new MaterialItemDetailResource($resource);

            return;
        }

        if ($resource instanceof MaterialList) {
            $params['resource']    = new MaterialListBriefResource($resource);

            return;
        }

    }

}
