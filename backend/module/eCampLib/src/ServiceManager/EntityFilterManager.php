<?php

namespace eCamp\Lib\ServiceManager;

use eCamp\Lib\Annotation\AnnotationsReader;
use eCamp\Lib\EntityFilter\EntityFilterInterface;
use Zend\ServiceManager\AbstractPluginManager;

class EntityFilterManager extends AbstractPluginManager {
    /**
     * @param $className
     *
     * @return EntityFilterInterface
     */
    public function getByEntityClass($className) {
        $filterName = AnnotationsReader::getEntityFilterAnnotation($className);
        if (null == $filterName) {
            return null;
        }

        return $this->get($filterName->filterClass);
    }
}
