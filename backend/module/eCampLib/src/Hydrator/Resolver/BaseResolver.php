<?php

namespace eCamp\Lib\Hydrator\Resolver;

use Closure;

abstract class BaseResolver
{
    /** @var Closure */
    private $resolver;
    protected $selection;

    public function __construct($resolver, $selection = []) {
        $this->resolver = $resolver;
        $this->selection = $selection;
    }

    public function resolve($object) {
        $resolver = $this->resolver;
        $value = $resolver($object);
        $value->_hydrateInfo_ = $this->selection;

        return $value;
    }

}