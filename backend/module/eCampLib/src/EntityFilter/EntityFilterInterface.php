<?php

namespace eCamp\Lib\EntityFilter;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

interface EntityFilterInterface {
    /**
     * @param $alias
     * @param $field
     *
     * @return Expr\Func
     */
    public function create(QueryBuilder $q, $alias, $field);
}
