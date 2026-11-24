<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\LoggerAwareInterface;
use ApiPlatform\Doctrine\Common\Filter\ManagerRegistryAwareInterface;
use ApiPlatform\Doctrine\Common\Filter\PropertyAwareFilterInterface;
use ApiPlatform\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\PropertyHelperTrait as OrmPropertyHelperTrait;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Exception\RuntimeException;
use ApiPlatform\Metadata\OpenApiParameterFilterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use ApiPlatform\OpenApi\Model\Parameter as OpenApiParameter;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Filters a computed property on the collection by date intervals.
 */
class ExpressionDateTimeFilter implements FilterInterface, DateFilterInterface, PropertyAwareFilterInterface, ManagerRegistryAwareInterface, LoggerAwareInterface, OpenApiParameterFilterInterface {
    use BackwardCompatibleFilterDescriptionTrait;
    use OrmPropertyHelperTrait;
    use PropertyHelperTrait;

    protected ?LoggerInterface $logger;

    public function __construct(
        protected ?ManagerRegistry $managerRegistry = null,
        ?LoggerInterface $logger = null,
        protected ?array $properties = null
    ) {
        $this->logger = $logger;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void {
        foreach ($context['filters'] as $property => $value) {
            $this->filterProperty((string) $property, $value, $queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
        }
    }

    public function hasManagerRegistry(): bool {
        return $this->managerRegistry instanceof ManagerRegistry;
    }

    public function getManagerRegistry(): ManagerRegistry {
        return $this->managerRegistry ?? throw new RuntimeException('ManagerRegistry must be initialized before accessing it.');
    }

    public function setManagerRegistry(ManagerRegistry $managerRegistry): void {
        $this->managerRegistry = $managerRegistry;
    }

    public function hasLogger(): bool {
        return null !== $this->logger;
    }

    public function getLogger(): LoggerInterface {
        return $this->logger ?? new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void {
        $this->logger = $logger;
    }

    public function getProperties(): ?array {
        return $this->properties;
    }

    public function setProperties(array $properties): void {
        $this->properties = $properties;
    }

    /**
     * @return OpenApiParameter[]
     */
    public function getOpenApiParameters(Parameter $parameter): array {
        return array_map(
            fn (string $period) => new OpenApiParameter(name: "{$parameter->getKey()}[{$period}]", in: 'query', schema: ['type' => 'string']),
            [self::PARAMETER_BEFORE, self::PARAMETER_STRICTLY_BEFORE, self::PARAMETER_AFTER, self::PARAMETER_STRICTLY_AFTER]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (
            !\is_array($value)
            || !$this->isPropertyEnabled($property)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        $expression = $this->properties[$property] ?? null;

        if (isset($value[DateFilterInterface::PARAMETER_BEFORE])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                DateFilterInterface::PARAMETER_BEFORE,
                $value[DateFilterInterface::PARAMETER_BEFORE],
                $expression,
                $resourceClass
            );
        }

        if (isset($value[DateFilterInterface::PARAMETER_STRICTLY_BEFORE])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                DateFilterInterface::PARAMETER_STRICTLY_BEFORE,
                $value[DateFilterInterface::PARAMETER_STRICTLY_BEFORE],
                $expression,
                $resourceClass
            );
        }

        if (isset($value[DateFilterInterface::PARAMETER_AFTER])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                DateFilterInterface::PARAMETER_AFTER,
                $value[DateFilterInterface::PARAMETER_AFTER],
                $expression,
                $resourceClass
            );
        }

        if (isset($value[DateFilterInterface::PARAMETER_STRICTLY_AFTER])) {
            $this->addWhere(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $field,
                DateFilterInterface::PARAMETER_STRICTLY_AFTER,
                $value[DateFilterInterface::PARAMETER_STRICTLY_AFTER],
                $expression,
                $resourceClass
            );
        }
    }

    /**
     * Adds the where clause based on the chosen expression.
     */
    protected function addWhere(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $field,
        string $operator,
        string $value,
        string $expression,
        string $resourceClass
    ) {
        try {
            $value = new \DateTime($value);
        } catch (\Exception) {
            // Silently ignore this filter if it can not be transformed to a \DateTime
            $this->getLogger()->notice('Invalid filter ignored', [
                'exception' => new InvalidArgumentException(sprintf('The field "%s" has a wrong date format. Use one accepted by the \DateTime constructor', $field)),
            ]);

            return;
        }

        $valueParameter = $queryNameGenerator->generateParameterName($field);
        $operatorValue = [
            DateFilterInterface::PARAMETER_BEFORE => '<=',
            DateFilterInterface::PARAMETER_STRICTLY_BEFORE => '<',
            DateFilterInterface::PARAMETER_AFTER => '>=',
            DateFilterInterface::PARAMETER_STRICTLY_AFTER => '>',
        ];
        $compiledExpression = $this->compileExpression($queryBuilder, $queryNameGenerator, $alias, $expression, $resourceClass);
        $baseWhere = sprintf('(%s) %s :%s', $compiledExpression, $operatorValue[$operator], $valueParameter);

        $queryBuilder->andWhere($baseWhere);

        $queryBuilder->setParameter($valueParameter, $value);
    }

    /**
     * Replaces placeholders in the given expression and adds joins for them.
     */
    protected function compileExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $alias,
        string $expression,
        string $resourceClass
    ): string {
        // Replace {} with the alias of the current entity
        $expression = preg_replace('/\{\}/', $alias, $expression);

        // Add joins for all {xyz.abc} property references in the expression
        $matches = [];
        if (preg_match_all('/\{([^\}]+\.[^\}]+)\}/', $expression, $matches)) {
            $relations = array_unique($matches[1]);

            // Replace each instance of {xyz.abc} with its respective joined alias
            foreach ($relations as $relation) {
                [$joinAlias, $property] = $this->addJoinsForNestedProperty($relation, $alias, $queryBuilder, $queryNameGenerator, $resourceClass, Join::INNER_JOIN);
                $expression = preg_replace('/\{'.preg_quote($relation, '/').'\}/', "{$joinAlias}.{$property}", $expression);
            }
        }

        return $expression;
    }

    private function isPropertyEnabled(string $property): bool {
        return \array_key_exists($property, $this->properties ?? []);
    }
}
