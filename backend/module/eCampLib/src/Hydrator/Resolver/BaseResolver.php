<?php

namespace eCamp\Lib\Hydrator\Resolver;

use Closure;

abstract class BaseResolver {
    /** @var Closure */
    private $resolver;
    private $linkResolver;
    protected $selection;

    public function __construct($resolver, $linkResolver, $selection = []) {
        $this->resolver = $resolver;
        $this->linkResolver = $linkResolver;
        $this->selection = $selection;
    }

    public function resolve($object) {
        $resolver = $this->resolver;
        $value = $resolver($object);
        $value->_hydrateInfo_ = $this->selection;

        return $value;
    }

    public function getLinks($object) {
        if ($this->linkResolver != null) {
            $linkResolver = $this->linkResolver;
            return $linkResolver($object);
        }
        return [];
    }
}
