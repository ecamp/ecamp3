<?php

namespace eCampApi;

use ZF\Hal\Collection;

class HalCollection extends Collection
{
    public $_hydrateInfo_;

    public function getCollection() {
        $collection = parent::getCollection();

        foreach ($collection as $item) {
            $item->_hydrateInfo_ = $this->_hydrateInfo_;
        }

        return $collection;
    }

}