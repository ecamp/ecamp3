<?php

namespace eCamp\Lib\Hydrator;

use Closure;
use eCamp\Lib\Hydrator\Resolver\CollectionLinkResolver;
use eCamp\Lib\Hydrator\Resolver\CollectionResolver;
use eCamp\Lib\Hydrator\Resolver\EntityLinkResolver;
use eCamp\Lib\Hydrator\Resolver\EntityResolver;

class Util {
    public static function Entity(Closure $resolver, array $selection = []): EntityResolver {
        return new EntityResolver($resolver, null, $selection);
    }

    public static function EntityLink(Closure $resolver): EntityLinkResolver {
        return new EntityLinkResolver($resolver, null);
    }

    public static function Collection(Closure $resolver, ?Closure $linkResolver, array $selection = []): CollectionResolver {
        return new CollectionResolver($resolver, $linkResolver, $selection);
    }

    public static function CollectionLink(Closure $resolver, ?Closure $linkResolver): CollectionLinkResolver {
        return new CollectionLinkResolver($resolver, $linkResolver);
    }
}
