<?php

namespace eCamp\Lib\Hydrator;

use DateTime;
use eCamp\Lib\Hydrator\Resolver\CollectionLinkResolver;
use eCamp\Lib\Hydrator\Resolver\CollectionResolver;
use eCamp\Lib\Hydrator\Resolver\EntityLinkResolver;
use eCamp\Lib\Hydrator\Resolver\EntityResolver;
use Exception;

class Util {
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
     * @return string in the ISO8601 format
     */
    public static function extractDate(?DateTime $date) {
        if (null == $date) {
            return null;
        }

        return $date->format('Y-m-d');
    }

    /**
     * @return string in the DATE_RFC3339 format
     */
    public static function extractDateTime(?DateTime $date) {
        if (null == $date) {
            return null;
        }

        return $date->format(DATE_RFC3339);
    }

    /**
     * @param $date string|int|DateTime
     * String in DateTime Format {@link https://php.net/manual/en/datetime.formats.php Date and Time Formats}
     * DateTime
     *
     * @throws Exception
     *
     * @return null|DateTime
     */
    public static function parseDate($date) {
        if ($date instanceof DateTime) {
            return $date;
        }

        if (is_string($date) && strlen($date) > 0) {
            return new DateTime($date);
        }

        return null;
    }

    /**
     * @param $date string|int|DateTime
     * String in DateTime Format {@link https://php.net/manual/en/datetime.formats.php Date and Time Formats}
     * DateTime
     *
     * @return null|DateTime
     */
    public static function parseDateTime($date) {
        if (is_string($date) && strlen($date) > 0) {
            return DateTime::createFromFormat(DATE_RFC3339, $date);
        }

        return null;
    }
}
