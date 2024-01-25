<?php

namespace App\Repository;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

interface CanFilterByCampInterface {
    public function filterByCamp(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $campId): void;
}
