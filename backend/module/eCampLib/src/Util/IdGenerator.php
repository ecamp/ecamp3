<?php

namespace eCamp\Lib\Util;

use RuntimeException;

class IdGenerator {
    public static function generateRandomHexString(int $length): string {
        try {
            return bin2hex(random_bytes($length / 2));
        } catch (\Exception $e) {
            throw new RuntimeException($e);
        }
    }
}
