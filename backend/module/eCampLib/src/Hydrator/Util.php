<?php

namespace eCamp\Lib\Hydrator;

use eCamp\Lib\Hydrator\Resolver\CollectionLinkResolver;
use eCamp\Lib\Hydrator\Resolver\CollectionResolver;
use eCamp\Lib\Hydrator\Resolver\EntityLinkResolver;
use eCamp\Lib\Hydrator\Resolver\EntityResolver;

class Util {
    /**
     * @param $resolver
     * @param array $selection
     */
    public static function Entity($resolver, $selection = []): EntityResolver {
        return new EntityResolver($resolver, null, $selection);
    }

    /**
     * @param $resolver
     */
    public static function EntityLink($resolver): EntityLinkResolver {
        return new EntityLinkResolver($resolver, null);
    }

    /**
     * @param $resolver
     * @param array $selection
     * @param mixed $linkResolver
     */
    public static function Collection($resolver, $linkResolver, $selection = []): CollectionResolver {
        return new CollectionResolver($resolver, $linkResolver, $selection);
    }

    public static function CollectionLink($resolver, $linkResolver): CollectionLinkResolver {
        return new CollectionLinkResolver($resolver, $linkResolver);
    }
}
