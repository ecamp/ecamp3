<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;

class QueryBuilderHelper {
    public static function copyParameters(QueryBuilder $target, QueryBuilder $source) {
        $params = $source->getParameters();

        foreach ($params->getKeys() as $key) {
            /** @var Parameter */
            $param = $params->get($key);
            $target->setParameter($param->getName(), $param->getValue());
        }
    }
}
