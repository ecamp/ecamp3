<?php

namespace eCampApi;

use eCamp\Lib\Hydrator\Resolver\BaseResolver;
use ZF\Hal\Extractor\EntityExtractor;

class HalEntityExtractor extends EntityExtractor
{
    public function extract($entity) {
        $data = parent::extract($entity);

        if (isset($entity->_hydrateInfo_)) {
            foreach ($entity->_hydrateInfo_ as $key => $resolver) {
                /** @var BaseResolver $resolver */
                $value = $resolver->resolve($entity);
                $data[$key] = $value;
            }
        }

        return $data;
    }
}