<?php

namespace eCamp\Lib\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class EntityFilter {
    /**
     * @Required
     * @var string
     */
    public $filterClass;
}
