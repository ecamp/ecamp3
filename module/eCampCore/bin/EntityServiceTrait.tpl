<?php

namespace eCamp\Core\EntityServiceTrait;

use eCamp\Core\EntityService;

trait [ServiceName]Trait
{
    /** @var EntityService\[ServiceName] */
    private $[ServiceVariable];

    public function set[ServiceName](EntityService\[ServiceName] $[ServiceVariable]) {
        $this->[ServiceVariable] = $[ServiceVariable];
    }

    public function get[ServiceName]() {
        return $this->[ServiceVariable];
    }

}
