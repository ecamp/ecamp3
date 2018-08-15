<?php

namespace eCamp\Core\EntityServiceAware;

use eCamp\Core\EntityService;

interface [ServiceName]Aware {
    /**
     * @return EntityService\[ServiceName]
     */
    public function get[ServiceName]();

    /**
     * @param EntityService\[ServiceName] $[ServiceVariable]
     */
    public function set[ServiceName](EntityService\[ServiceName] $[ServiceVariable]);
}
