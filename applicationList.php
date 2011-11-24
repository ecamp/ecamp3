<?php

namespace CoreApi\Entity;

class LoginList implements ArrayAccess
{

    public function __construct(array $entityList = null)
    {
        $this->entityList = $entityList;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->entityList);
    }

    /**
     * @return \CoreApi\Entity\Login
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? new \CoreApi\Entity\Login($this->entityList[$offset]) : null;
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception("This is a Readonly List);
    }

    public function offsetUnset($offset)
    {
        throw new \Exception("This is a Readonly List);
    }


}
