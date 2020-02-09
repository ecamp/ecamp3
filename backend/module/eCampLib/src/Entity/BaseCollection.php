<?php

namespace eCamp\Lib\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use Zend\Paginator\Paginator;

abstract class BaseCollection extends Paginator {
    public function __construct($adapter, Criteria $criteria = null) {
        if ($adapter instanceof Selectable) {
            $adapter = new SelectableAdapter($adapter, $criteria);
        }

        parent::__construct($adapter);
    }
}
