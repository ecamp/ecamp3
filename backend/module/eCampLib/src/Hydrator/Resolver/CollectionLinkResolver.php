<?php

namespace eCamp\Lib\Hydrator\Resolver;

use eCamp\Lib\Entity\EntityLinkCollection;

class CollectionLinkResolver extends BaseResolver {
    public function resolve($object) {
        $collection = parent::resolve($object);
        $value = new EntityLinkCollection($collection);
        $value->_hydrateInfo_ = $this->selection;

        return $value;
    }
}
