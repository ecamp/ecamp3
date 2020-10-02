<?php

namespace eCamp\Lib\Hydrator;

use DateTime;
use eCamp\Lib\Hydrator\Resolver\CollectionLinkResolver;
use eCamp\Lib\Hydrator\Resolver\CollectionResolver;
use eCamp\Lib\Hydrator\Resolver\EntityLinkResolver;
use eCamp\Lib\Hydrator\Resolver\EntityResolver;
use Exception;

class Util {
    const MillisecondsInASecond = 1000;

    /**
     * @param $resolver
     * @param array $selection
     *
     * @return EntityResolver
     */
    public static function Entity($resolver, $selection = []) {
        return new EntityResolver($resolver, null, $selection);
    }

    /**
     * @param $resolver
     *
     * @return EntityLinkResolver
     */
    public static function EntityLink($resolver) {
        return new EntityLinkResolver($resolver, null);
    }

    /**
     * @param $resolver
     * @param array $selection
     * @param mixed $linkResolver
     *
     * @return CollectionResolver
     */
    public static function Collection($resolver, $linkResolver, $selection = []) {
        return new CollectionResolver($resolver, $linkResolver, $selection);
    }

    public static function CollectionLink($resolver, $linkResolver) {
        return new CollectionLinkResolver($resolver, $linkResolver);
    }

    /**
     * @return int Milliseconds since 1970-01-01T00:00:00 UTC
     */
    public static function extractTimestamp(?DateTime $date) {
        if (null == $date) {
            return null;
        }

        return $date->getTimestamp() * self::MillisecondsInASecond;
    }

    /**
     * @param $date string|int|DateTime
     * String in DateTime Format {@link https://php.net/manual/en/datetime.formats.php Date and Time Formats}
     * Int in Milliseconds Timestamp
     * DateTime
     *
     * @throws Exception
     *
     * @return null|DateTime
     */
    public static function parseDate($date) {
        $result = null;

        if ($date instanceof DateTime) {
            $result = $date;
        }

        if (is_string($date) && strlen($date) > 0) {
            $result = new DateTime($date);
        }

        if (is_int($date) && $date >= 0) {
            $result = new DateTime('@'.($date / self::MillisecondsInASecond));
        }

        return $result;
    }
}
