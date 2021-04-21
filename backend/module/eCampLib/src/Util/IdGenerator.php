<?php

namespace eCamp\Lib\Util;

use Laminas\Math\Rand;

class IdGenerator {
    public static function generateRandomHexString(int $length): string {
        return bin2hex(Rand::getBytes($length / 2));
    }
}
