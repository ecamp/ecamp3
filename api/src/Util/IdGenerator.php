<?php

namespace App\Util;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class IdGenerator extends AbstractIdGenerator {
    public static function generateRandomHexString(int $length): string {
        return bin2hex(random_bytes($length / 2));
    }

    public function generate(EntityManager $em, $entity): string {
        return IdGenerator::generateRandomHexString(12);
    }
}
