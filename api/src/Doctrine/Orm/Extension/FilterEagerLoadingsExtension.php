<?php

namespace App\Doctrine\Orm\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ToManyInverseSideMapping;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

final readonly class FilterEagerLoadingsExtension implements QueryCollectionExtensionInterface {
    public function __construct(private QueryCollectionExtensionInterface $decorated) {}

    #[\Override]
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ?string $resourceClass = null,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (null === $resourceClass) {
            throw new InvalidArgumentException('The "$resourceClass" parameter must not be null');
        }

        $em = $queryBuilder->getEntityManager();
        $classMetadata = $em->getClassMetadata($resourceClass);

        // If no where part, nothing to do
        $wherePart = $queryBuilder->getDQLPart('where');

        if (!$wherePart) {
            return;
        }

        $joinParts = $queryBuilder->getDQLPart('join');
        $originAlias = $queryBuilder->getRootAliases()[0];

        if (!$joinParts || !isset($joinParts[$originAlias])) {
            return;
        }

        $aliasMap = $this->buildAliasMap($em, $classMetadata, $joinParts, $originAlias);
        $usesToManyAlias = $this->usesAnyToMany($aliasMap, $wherePart);

        if ($usesToManyAlias) {
            $this->decorated->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
        }
    }

    private function buildAliasMap(EntityManagerInterface $em, ClassMetadata $classMetadata, array $joinParts, $originAlias) {
        // $alias => [ ClassMetadata $classMetadata, bool $is[One/Many]ToManyJoin ]
        $aliasMap = [$originAlias => [$classMetadata, false]];
        $joins = $joinParts[$originAlias];

        foreach ($joins as $join) {
            // @var Join $join
            [$fromAlias, $fromProperty] = explode('.', $join->getJoin(), 2);
            $toAlias = $join->getAlias();

            $fromClassMetadata = $aliasMap[$fromAlias][0];
            $association = $fromClassMetadata->getAssociationMapping($fromProperty);
            $m = $em->getClassMetadata($association['targetEntity']);

            $isToMany = $aliasMap[$fromAlias][1] || ($association instanceof ToManyInverseSideMapping);

            $aliasMap[$toAlias] = [$m, $isToMany];
        }

        return $aliasMap;
    }

    private function usesAnyToMany($toManyAliases, $wherePart) {
        if ($wherePart instanceof Andx) {
            return $this->usesAnyToManyAndx($toManyAliases, $wherePart);
        }
        if ($wherePart instanceof Orx) {
            return $this->usesAnyToManyOrx($toManyAliases, $wherePart);
        }
        if ($wherePart instanceof Comparison) {
            return $this->usesAnyToManyComparison($toManyAliases, $wherePart);
        }
        if ($wherePart instanceof Func) {
            return $this->usesAnyToManyFunc($toManyAliases, $wherePart);
        }
        if (is_string($wherePart)) {
            return $this->usesAnyToManyString($toManyAliases, $wherePart);
        }

        throw new \Exception('Not Implemented: FilterEagerLoadingsExtension->usesToManyAlias for '.$wherePart);
    }

    private function usesAnyToManyAndx($toManyAliases, Andx $and) {
        return array_any($and->getParts(), fn ($part) => $this->usesAnyToMany($toManyAliases, $part));
    }

    private function usesAnyToManyOrx($toManyAliases, Orx $or) {
        return array_any($or->getParts(), fn ($part) => $this->usesAnyToMany($toManyAliases, $part));
    }

    private function usesAnyToManyComparison($toManyAliases, Comparison $comparison) {
        return
            $this->usesAnyToMany($toManyAliases, $comparison->getLeftExpr())
            || $this->usesAnyToMany($toManyAliases, $comparison->getRightExpr());
    }

    private function usesAnyToManyFunc($toManyAliases, Func $func) {
        return array_any($func->getArguments(), fn ($argument) => $this->usesAnyToMany($toManyAliases, $argument));
    }

    private function usesAnyToManyString($toManyAliases, string $comparison) {
        $elements = explode('.', $comparison, 2);

        if (2 == count($elements)) {
            $alias = $elements[0];
            if (isset($toManyAliases[$alias])) {
                return $toManyAliases[$alias][1];
            }
        }

        return false;
    }
}
