<?php

namespace eCamp\Lib\Hydrator;

class Util
{
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
