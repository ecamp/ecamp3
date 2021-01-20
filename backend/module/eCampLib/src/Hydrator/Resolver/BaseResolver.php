<?php

namespace eCamp\Lib\Hydrator\Resolver;

use Closure;

abstract class BaseResolver {
    protected $selection;
    /** @var Closure */
    private $resolver;
    private $linkResolver;

    public function __construct($resolver, $linkResolver, $selection = []) {
        $this->resolver = $resolver;
        $this->linkResolver = $linkResolver;
        $this->selection = $selection;
    }

    public function resolve($object) {
        $resolver = $this->resolver;
        $value = $resolver($object);
        if (null != $value) {
            $value->_hydrateInfo_ = $this->selection;
        }

        return $value;
    }

    public function getLinks($object) {
        if (null != $this->linkResolver) {
            $linkResolver = $this->linkResolver;

            return $linkResolver($object);
        }

        return [];
    }
}
