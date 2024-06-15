<?php

namespace App\Util;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class IdGenerator extends AbstractIdGenerator {
    public static function generateRandomHexString(int $length): string {
        return bin2hex(random_bytes($length / 2));
    }

    public function generateId(EntityManagerInterface $em, ?object $entity): mixed {
        return IdGenerator::generateRandomHexString(12);
    }
}
