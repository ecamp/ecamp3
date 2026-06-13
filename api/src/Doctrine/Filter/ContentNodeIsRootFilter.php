<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\ContentNode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\TypeInfo\Type;

final class ContentNodeIsRootFilter extends AbstractFilter {
    public const string IS_ROOT_QUERY_NAME = 'isRoot';

    public function __construct(
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    #[\Override]
    public function getDescription(string $resourceClass): array {
        return ['isRoot' => [
            'property' => self::IS_ROOT_QUERY_NAME,
            'type' => Type::bool()->__toString(),
            'required' => false,
        ]];
    }

    #[\Override]
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (ContentNode::class !== $resourceClass && !is_subclass_of($resourceClass, ContentNode::class)) {
            throw new \Exception("ContentNodeIsRootFilter can only be applied to entities of type ContentNode (received: {$resourceClass}).");
        }

        if (self::IS_ROOT_QUERY_NAME !== $property) {
            return;
        }

        $isRootFilter = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($isRootFilter) {
            $queryBuilder->andWhere("{$rootAlias}.parent IS NULL");
        } else {
            $queryBuilder->andWhere("{$rootAlias}.parent IS NOT NULL");
        }
    }
}
