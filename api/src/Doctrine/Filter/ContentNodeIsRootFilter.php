<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use App\Entity\ContentNode;
use Doctrine\ORM\QueryBuilder;

final class ContentNodeIsRootFilter implements FilterInterface, OpenApiParameterFilterInterface {
    use BackwardCompatibleFilterDescriptionTrait;

    public function getOpenApiParameters(Parameter $parameter): OpenApiParameter {
        return new OpenApiParameter(name: $parameter->getKey(), in: 'query', schema: ['type' => 'boolean']);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (ContentNode::class !== $resourceClass && !is_subclass_of($resourceClass, ContentNode::class)) {
            throw new \Exception("ContentNodeIsRootFilter can only be applied to entities of type ContentNode (received: {$resourceClass}).");
        }
        $value = $context['parameter']->getValue();
        $isRootFilter = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($isRootFilter) {
            $queryBuilder->andWhere("{$rootAlias}.parent IS NULL");
        } else {
            $queryBuilder->andWhere("{$rootAlias}.parent IS NOT NULL");
        }
    }
}
