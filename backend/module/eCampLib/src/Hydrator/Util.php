<?php

namespace eCamp\Lib\Hydrator;

use eCamp\Lib\Hydrator\Resolver\CollectionLinkResolver;
use eCamp\Lib\Hydrator\Resolver\CollectionResolver;
use eCamp\Lib\Hydrator\Resolver\EntityResolver;
use eCamp\Lib\Hydrator\Resolver\EntityLinkResolver;

class Util {

    /**
     * @param $resolver
     * @param array $selection
     * @return EntityResolver
     */
    public static function Entity($resolver, $selection = []) {
        return new EntityResolver($resolver, $selection);
    }

    /**
     * @param $resolver
     * @return EntityLinkResolver
     */
    public static function EntityLink($resolver) {
        return new EntityLinkResolver($resolver);
    }

    /**
     * @param $resolver
     * @param array $selection
     * @return CollectionResolver
     */
    public static function Collection($resolver, $selection = []) {
        return new CollectionResolver($resolver, $selection);
    }

    public static function CollectionLink($resolver) {
        return new CollectionLinkResolver($resolver);
    }




    public static function extractDate(\DateTime $date) {
        return $date->format('Y-m-d');
    }

    public static function extractDateTime(\DateTime $date) {
        return $date->format('Y-m-d H:i:s');
    }

    public static function parseDate($date) {
        $result = null;

        if ($date instanceof \DateTime) {
            $result = $date;
        }

        if (is_string($date)) {
            $result = new \DateTime($date);
        }

        return $result;
    }
}
