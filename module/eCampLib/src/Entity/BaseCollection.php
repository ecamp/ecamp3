<?php

namespace eCamp\Lib\Entity;


use Doctrine\Common\Collections\Selectable;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use Zend\Paginator\Paginator;

abstract class BaseCollection extends Paginator
{
    public function __construct($adapter) {
        if ($adapter instanceof Selectable) {
            $adapter = new SelectableAdapter($adapter);
        }

        parent::__construct($adapter);
    }

}