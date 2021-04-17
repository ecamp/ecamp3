<?php

namespace eCamp\Lib\Entity;

use Doctrine\Common\Collections\Selectable;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use Laminas\Paginator\Paginator;

class EntityLinkCollection extends Paginator {
    public function __construct($adapter) {
        if ($adapter instanceof Selectable) {
            $adapter = new SelectableAdapter($adapter);
        }
        if ($adapter instanceof Paginator) {
            $adapter = $adapter->getAdapter();
        }

        parent::__construct($adapter);
    }

    public function getIterator(): \Traversable {
        $items = parent::getIterator();

        foreach ($items as $item) {
            if ($item instanceof BaseEntity) {
                yield new EntityLink($item);
            } else {
                yield $item;
            }
        }
    }
}
