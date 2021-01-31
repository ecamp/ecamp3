<?php

namespace eCamp\Lib\ServiceManager;

use eCamp\Lib\Annotation\AnnotationsReader;
use eCamp\Lib\EntityFilter\EntityFilterInterface;
use Laminas\ServiceManager\AbstractPluginManager;

class EntityFilterManager extends AbstractPluginManager {
    public function getByEntityClass(string $className): ?EntityFilterInterface {
        $filterName = AnnotationsReader::getEntityFilterAnnotation($className);
        if (null == $filterName) {
            return null;
        }

        return $this->get($filterName->filterClass);
    }
}
