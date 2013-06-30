<?php

namespace EcampApi\Serializer;

use EcampLib\Entity\BaseEntity;

abstract class BaseSerializer
{
    protected $format;
    protected $router;

    public function __construct($format, $router)
    {
        $this->format = $format;
        $this->router = $router;
    }

    public function __invoke($entity)
    {
        if (is_array($entity)) {
            $list = array();
            foreach ($entity as $k => $v) {
                $list[$k] = $this($v);
            }

            return $list;
        }
		
        if ($entity instanceof BaseEntity) {
            return $this->serialize($entity);
        }

        return (string) $entity;
    }

    abstract protected function serialize($entity);

}
