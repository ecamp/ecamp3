<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Activity;
use App\Entity\Camp;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;
use App\Entity\Period;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class MaterialItemCampFilter extends AbstractFilter {
    public const CAMP_QUERY_NAME = 'camp';

    public function __construct(
        private IriConverterInterface $iriConverter,
        ManagerRegistry $managerRegistry,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array {
        $description = [];
        $description['camp'] = [
            'property' => self::CAMP_QUERY_NAME,
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
        Operation $operation = null,
        array $context = []
    ): void {
        if (MaterialItem::class !== $resourceClass) {
            throw new \Exception("MaterialItemCampFilter can only be applied to entities of type MaterialItem (received: {$resourceClass}).");
        }

        if (self::CAMP_QUERY_NAME !== $property) {
            return;
        }

        // load period from query parameter value
        $camp = $this->iriConverter->getResourceFromIri($value);

        // generate alias to avoid interference with other filters
        $campParameterName = $queryNameGenerator->generateParameterName($property);
        $periodJoinAlias = $queryNameGenerator->generateJoinAlias('period');
        $campActivityJoinAlias = $queryNameGenerator->generateJoinAlias('camp_for_activity');
        $campPeriodJoinAlias = $queryNameGenerator->generateJoinAlias('camp_for_period');
        $materialNodeJoinAlias = $queryNameGenerator->generateJoinAlias('materialNode');
        $rootJoinAlias = $queryNameGenerator->generateJoinAlias('root');
        $activityJoinAlias = $queryNameGenerator->generateJoinAlias('activity');

        $rootAlias = $queryBuilder->getRootAliases()[0];

        /** @var EntityRepository $materialNodeRepository */
        $materialNodeRepository = $this->getManagerRegistry()->getRepository(MaterialNode::class);

        /** @var EntityRepository $periodRepository */
        $periodRepository = $this->getManagerRegistry()->getRepository(Period::class);
        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            // item directly attached to Period
            $queryBuilder->expr()->in(
                "{$rootAlias}.period",
                $periodRepository
                    ->createQueryBuilder($periodJoinAlias)
                    ->select("{$periodJoinAlias}.id")
                    ->join(Camp::class, $campPeriodJoinAlias, Join::WITH, "{$periodJoinAlias}.camp = {$campPeriodJoinAlias}.id")
                    ->where($queryBuilder->expr()->eq("{$campPeriodJoinAlias}.id", ":{$campParameterName}"))
                    ->getDQL()
            ),
            // item part of any scheduleEntry in Period
            $queryBuilder->expr()->in(
                "{$rootAlias}.materialNode",
                $materialNodeRepository
                    ->createQueryBuilder($materialNodeJoinAlias)
                    ->select("{$materialNodeJoinAlias}.id")
                    ->join("{$materialNodeJoinAlias}.root", $rootJoinAlias)
                    ->join(Activity::class, $activityJoinAlias, Join::WITH, "{$activityJoinAlias}.rootContentNode = {$rootJoinAlias}.id")
                    ->join(Camp::class, $campActivityJoinAlias, Join::WITH, "{$campActivityJoinAlias}.id = {$activityJoinAlias}.camp")
                    ->where($queryBuilder->expr()->eq("{$campActivityJoinAlias}.id", ":{$campParameterName}"))
                    ->getDQL()
            )
        ));

        $queryBuilder->setParameter($campParameterName, $camp);
    }
}
