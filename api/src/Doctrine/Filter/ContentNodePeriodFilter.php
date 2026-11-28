<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Activity;
use App\Entity\ContentNode;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

final class ContentNodePeriodFilter implements FilterInterface {
    use BackwardCompatibleFilterDescriptionTrait;

    public function __construct(private readonly IriConverterInterface $iriConverter) {}

    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $parameter = $context['parameter'];
        $property = $parameter->getKey();
        $value = $parameter->getValue();

        if (ContentNode::class !== $resourceClass && !is_subclass_of($resourceClass, ContentNode::class)) {
            throw new \Exception("ContentNodePeriodFilter can only be applied to entities of type ContentNode (received: {$resourceClass}).");
        }

        // load period from query parameter value
        $period = $this->iriConverter->getResourceFromIri($value);

        // generate alias to avoid interference with other filters
        $periodParameterName = $queryNameGenerator->generateParameterName($property);
        $activityJoinAlias = $queryNameGenerator->generateJoinAlias('activity');
        $scheduleEntryJoinAlias = $queryNameGenerator->generateJoinAlias('scheduleEntry');

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $rootQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $rootQry
            ->select("identity({$activityJoinAlias}.rootContentNode)")
            ->from(Activity::class, $activityJoinAlias)
            ->innerJoin("{$activityJoinAlias}.scheduleEntries", $scheduleEntryJoinAlias, Join::WITH, $queryBuilder->expr()->eq("{$scheduleEntryJoinAlias}.period", ":{$periodParameterName}"))
        ;

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.root", $rootQry->getDQL()));
        $queryBuilder->setParameter($periodParameterName, $period);
    }
}
