<?php

namespace App\Util;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderHelper {
    public static function findOrAddInnerRootJoinAlias(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $association) {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        return QueryBuilderHelper::findOrAddInnerJoinAlias($queryBuilder, $queryNameGenerator, $rootAlias, $association);
    }

    /**
     * @psalm-suppress NoValue
     */
    public static function findOrAddInnerJoinAlias(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $association) {
        $allJoins = $queryBuilder->getDQLPart('join');

        $joins = array_filter(
            $allJoins[$alias] ?? [],
            fn ($j) => 'INNER' == $j->getJoinType() && $j->getJoin() == "{$alias}.{$association}"
        );

        if ([] === $joins) {
            $innerAlias = $queryNameGenerator->generateJoinAlias($association);
            $queryBuilder->join("{$alias}.{$association}", $innerAlias);
        } else {
            $join = reset($joins);
            $innerAlias = $join->getAlias();
        }

        return $innerAlias;
    }
}
