<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Activity;
use App\Entity\ContentNode\MaterialNode;
use App\Entity\MaterialItem;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class MaterialItemPeriodFilter extends AbstractContextAwareFilter {
    public const PERIOD_QUERY_NAME = 'period';

    public function __construct(
        private IriConverterInterface $iriConverter,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack = null,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
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

    protected function filterProperty(string $property, $value, QueryBuilder $q, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null) {
        if (MaterialItem::class !== $resourceClass) {
            throw new \Exception("MaterialItemPeriodFilter can only be applied to entities of type MaterialItem (received: {$resourceClass}).");
        }

        if (self::PERIOD_QUERY_NAME !== $property) {
            return;
        }

        // load period from query parameter value
        $period = $this->iriConverter->getItemfromIri($value);

        // generate alias to avoid interference with other filters
        $periodParameterName = $queryNameGenerator->generateParameterName($property);
        $materialNodeJoinAlias = $queryNameGenerator->generateJoinAlias('materialNode');
        $rootJoinAlias = $queryNameGenerator->generateJoinAlias('root');
        $ownerJoinAlias = $queryNameGenerator->generateJoinAlias('owner');
        $activityJoinAlias = $queryNameGenerator->generateJoinAlias('activity');
        $scheduleEntryJoinAlias = $queryNameGenerator->generateJoinAlias('scheduleEntry');

        $rootAlias = $q->getRootAliases()[0];

        /** @var EntityRepository $materialNodeRepository */
        $materialNodeRepository = $this->getManagerRegistry()->getRepository(MaterialNode::class);
        $q->andWhere($q->expr()->orX(
             // item directly attached to Period
            $q->expr()->eq("{$rootAlias}.period", ":{$periodParameterName}"),
            // item part of any scheduleEntry in Period
            $q->expr()->in(
                "{$rootAlias}.materialNode",
                $materialNodeRepository
                    ->createQueryBuilder($materialNodeJoinAlias)
                    ->select("{$materialNodeJoinAlias}.id")
                    ->join("{$materialNodeJoinAlias}.root", $rootJoinAlias)
                    ->join("{$rootJoinAlias}.owner", $ownerJoinAlias)
                    ->join(Activity::class, $activityJoinAlias, Join::WITH, "{$activityJoinAlias}.id = {$ownerJoinAlias}.id")
                    ->join("{$activityJoinAlias}.scheduleEntries", $scheduleEntryJoinAlias)
                    ->where($q->expr()->eq("{$scheduleEntryJoinAlias}.period", ":{$periodParameterName}"))
                    ->getDQL()
            )
        ));

        $q->setParameter($periodParameterName, $period);
    }
}
