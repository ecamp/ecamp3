<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\MaterialItem;
use App\Entity\PeriodMaterialItem;
use Doctrine\ORM\QueryBuilder;

final class MaterialItemPeriodFilter implements FilterInterface {
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

        if (MaterialItem::class !== $resourceClass) {
            throw new \Exception("MaterialItemPeriodFilter can only be applied to entities of type MaterialItem (received: {$resourceClass}).");
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
