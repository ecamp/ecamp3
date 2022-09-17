<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\DateFilterTrait;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * Filters a computed property on the collection by date intervals.
 */
class ExpressionDateTimeFilter extends AbstractFilter implements DateFilterInterface {
    use DateFilterTrait;

    /**
     * {@inheritdoc}
     */
    public function getDescription(string $resourceClass): array {
        $description = [];

        $properties = $this->getProperties();
        if (null === $properties) {
            $properties = [];
        }

        foreach ($properties as $property => $expression) {
            if (!$expression) {
                continue;
            }

            $description += $this->getFilterDescription($property, DateFilterInterface::PARAMETER_BEFORE);
            $description += $this->getFilterDescription($property, DateFilterInterface::PARAMETER_STRICTLY_BEFORE);
            $description += $this->getFilterDescription($property, DateFilterInterface::PARAMETER_AFTER);
            $description += $this->getFilterDescription($property, DateFilterInterface::PARAMETER_STRICTLY_AFTER);
        }

        return $description;
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
        Operation $operation = null,
        array $context = []
    ): void {
        if (
            !\is_array($value)
            || !$this->isPropertyEnabled($property, $resourceClass)
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
        } catch (\Exception $e) {
            // Silently ignore this filter if it can not be transformed to a \DateTime
            $this->logger->notice('Invalid filter ignored', [
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
            $relations = array_unique($matches[1] ?? []);

            // Replace each instance of {xyz.abc} with its respective joined alias
            foreach ($relations as $relation) {
                [$joinAlias, $property] = $this->addJoinsForNestedProperty($relation, $alias, $queryBuilder, $queryNameGenerator, $resourceClass, Join::INNER_JOIN);
                $expression = preg_replace('/\{'.preg_quote($relation, '/').'\}/', "{$joinAlias}.{$property}", $expression);
            }
        }

        return $expression;
    }
}
