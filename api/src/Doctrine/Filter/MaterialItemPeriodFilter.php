<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\MaterialItem;
use App\Entity\PeriodMaterialItem;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\TypeInfo\Type;

final class MaterialItemPeriodFilter extends AbstractFilter {
    public const string PERIOD_QUERY_NAME = 'period';

    public function __construct(
        private readonly IriConverterInterface $iriConverter,
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
        return ['period' => [
            'property' => self::PERIOD_QUERY_NAME,
            'type' => Type::string()->__toString(),
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
        if (MaterialItem::class !== $resourceClass) {
            throw new \Exception("MaterialItemPeriodFilter can only be applied to entities of type MaterialItem (received: {$resourceClass}).");
        }

        if (self::PERIOD_QUERY_NAME !== $property) {
            return;
        }

        // load period from query parameter value
        $period = $this->iriConverter->getResourceFromIri($value);

        // generate alias to avoid interference with other filters
        $periodParameterName = $queryNameGenerator->generateParameterName($property);
        $periodMaterialItems = $queryNameGenerator->generateJoinAlias('periodMaterialItem');

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $materialItemQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $materialItemQry->select("identity({$periodMaterialItems}.materialItem)");
        $materialItemQry->from(PeriodMaterialItem::class, $periodMaterialItems);
        $materialItemQry->where($queryBuilder->expr()->eq("{$periodMaterialItems}.period", ":{$periodParameterName}"));

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}", $materialItemQry->getDQL()));
        $queryBuilder->setParameter($periodParameterName, $period);
    }
}
