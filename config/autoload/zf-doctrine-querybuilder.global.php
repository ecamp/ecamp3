<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c] 2013-2016 Zend Technologies USA Inc. (http://www.zend.com]
 */

namespace ZF\Doctrine\QueryBuilder;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'zf-apigility-doctrine-query-provider' => [
        'aliases' => [
            'default_orm' => Query\Provider\DefaultOrm::class,
        ],
        'factories' => [
            Query\Provider\DefaultOrm::class => Query\Provider\DefaultOrmFactory::class,
        ],
    ],

    'zf-doctrine-querybuilder-options' => [
        'filter_key' => 'filter',
        'order_by_key' => 'order-by',
    ],
    'zf-doctrine-querybuilder-orderby-orm' => [
        'aliases' => [
            'field' => OrderBy\ORM\Field::class,
        ],
        'factories' => [
            OrderBy\ORM\Field::class => InvokableFactory::class,
        ],
    ],
    'zf-doctrine-querybuilder-orderby-odm' => [
        'aliases' => [
            'field' => OrderBy\ODM\Field::class,
        ],
        'factories' => [
            OrderBy\ODM\Field::class => InvokableFactory::class,
        ],
    ],
    'zf-doctrine-querybuilder-filter-orm' => [
        'aliases' => [
            'eq'         => Filter\ORM\Equals::class,
            'neq'        => Filter\ORM\NotEquals::class,
            'lt'         => Filter\ORM\LessThan::class,
            'lte'        => Filter\ORM\LessThanOrEquals::class,
            'gt'         => Filter\ORM\GreaterThan::class,
            'gte'        => Filter\ORM\GreaterThanOrEquals::class,
            'isnull'     => Filter\ORM\IsNull::class,
            'isnotnull'  => Filter\ORM\IsNotNull::class,
            'in'         => Filter\ORM\In::class,
            'notin'      => Filter\ORM\NotIn::class,
            'between'    => Filter\ORM\Between::class,
            'like'       => Filter\ORM\Like::class,
            'notlike'    => Filter\ORM\NotLike::class,
            'ismemberof' => Filter\ORM\IsMemberOf::class,
            'orx'        => Filter\ORM\OrX::class,
            'andx'       => Filter\ORM\AndX::class,
        ],
        'factories' => [
            Filter\ORM\Equals::class              => InvokableFactory::class,
            Filter\ORM\NotEquals::class           => InvokableFactory::class,
            Filter\ORM\LessThan::class            => InvokableFactory::class,
            Filter\ORM\LessThanOrEquals::class    => InvokableFactory::class,
            Filter\ORM\GreaterThan::class         => InvokableFactory::class,
            Filter\ORM\GreaterThanOrEquals::class => InvokableFactory::class,
            Filter\ORM\IsNull::class              => InvokableFactory::class,
            Filter\ORM\IsNotNull::class           => InvokableFactory::class,
            Filter\ORM\In::class                  => InvokableFactory::class,
            Filter\ORM\NotIn::class               => InvokableFactory::class,
            Filter\ORM\Between::class             => InvokableFactory::class,
            Filter\ORM\Like::class                => InvokableFactory::class,
            Filter\ORM\NotLike::class             => InvokableFactory::class,
            Filter\ORM\IsMemberOf::class          => InvokableFactory::class,
            Filter\ORM\OrX::class                 => InvokableFactory::class,
            Filter\ORM\AndX::class                => InvokableFactory::class,
        ],
    ],
    'zf-doctrine-querybuilder-filter-odm' => [
        'aliases' => [
            'eq'        => Filter\ODM\Equals::class,
            'neq'       => Filter\ODM\NotEquals::class,
            'lt'        => Filter\ODM\LessThan::class,
            'lte'       => Filter\ODM\LessThanOrEquals::class,
            'gt'        => Filter\ODM\GreaterThan::class,
            'gte'       => Filter\ODM\GreaterThanOrEquals::class,
            'isnull'    => Filter\ODM\IsNull::class,
            'isnotnull' => Filter\ODM\IsNotNull::class,
            'in'        => Filter\ODM\In::class,
            'notin'     => Filter\ODM\NotIn::class,
            'between'   => Filter\ODM\Between::class,
            'like'      => Filter\ODM\Like::class,
            'regex'     => Filter\ODM\Regex::class,
        ],
        'factories' => [
            Filter\ODM\Equals::class              => InvokableFactory::class,
            Filter\ODM\NotEquals::class           => InvokableFactory::class,
            Filter\ODM\LessThan::class            => InvokableFactory::class,
            Filter\ODM\LessThanOrEquals::class    => InvokableFactory::class,
            Filter\ODM\GreaterThan::class         => InvokableFactory::class,
            Filter\ODM\GreaterThanOrEquals::class => InvokableFactory::class,
            Filter\ODM\IsNull::class              => InvokableFactory::class,
            Filter\ODM\IsNotNull::class           => InvokableFactory::class,
            Filter\ODM\In::class                  => InvokableFactory::class,
            Filter\ODM\NotIn::class               => InvokableFactory::class,
            Filter\ODM\Between::class             => InvokableFactory::class,
            Filter\ODM\Like::class                => InvokableFactory::class,
            Filter\ODM\Regex::class               => InvokableFactory::class,
        ],
    ],
];
