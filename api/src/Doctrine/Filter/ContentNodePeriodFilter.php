<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Activity;
use App\Entity\ContentNode;
use App\Repository\FiltersByCampCollaboration;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ContentNodePeriodFilter extends AbstractFilter {
    use FiltersByCampCollaboration;

    public const PERIOD_QUERY_NAME = 'period';

    public function __construct(
        private IriConverterInterface $iriConverter,
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array {
        $description = [];
        $description['period'] = [
            'property' => self::PERIOD_QUERY_NAME,
            'type' => Type::BUILTIN_TYPE_STRING,
            'required' => false,
        ];

        return $description;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (ContentNode::class !== $resourceClass) {
            throw new \Exception("ContentNodePeriodFilter can only be applied to entities of type ContentNode (received: {$resourceClass}).");
        }

        if (self::PERIOD_QUERY_NAME !== $property) {
            return;
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
