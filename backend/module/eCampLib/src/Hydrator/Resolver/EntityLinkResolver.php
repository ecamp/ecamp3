<?php

namespace eCamp\Lib\Hydrator\Resolver;

use eCamp\Lib\Entity\EntityLink;

class EntityLinkResolver extends BaseResolver {
    public function resolve($object) {
        $entity = parent::resolve($object);
        $value = EntityLink::Create($entity);
        unset($value->_hydrateInfo_);

        return $value;
    }
}