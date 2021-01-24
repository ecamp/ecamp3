<?php

namespace eCamp\Lib\EntityFilter;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

interface EntityFilterInterface {
    /**
     * @param $alias
     * @param $field
     */
    public function create(QueryBuilder $q, $alias, $field): Expr\Func;
}
