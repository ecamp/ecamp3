<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\BackwardCompatibleFilterDescriptionTrait;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Util\QueryBuilderHelper;
use Doctrine\ORM\QueryBuilder;

final class CampCollaboratorFilter implements FilterInterface {
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
        $value = $parameter->getValue();

        if (!is_a($resourceClass, BelongsToCampInterface::class, true)) {
            throw new \Exception("CampCollaboratorFilter can only be applied to entities which implement BelongsToCampInterface (received: {$resourceClass}).");
        }

        $campAlias = Camp::class === $resourceClass
            ? $queryBuilder->getRootAliases()[0]
            : QueryBuilderHelper::findOrAddInnerRootJoinAlias($queryBuilder, $queryNameGenerator, 'camp');

        // load user from query parameter value
        $collaborator = $this->iriConverter->getResourceFromIri($value);

        $campsQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $campsQry->select('identity(cc.camp)');
        $campsQry->from(CampCollaboration::class, 'cc');
        $campsQry->andWhere('cc.user = :collaborator');
        $campsQry->andWhere('cc.status = :status_established');

        $queryBuilder->andWhere($queryBuilder->expr()->in($campAlias, $campsQry->getDQL()));
        $queryBuilder->setParameter('collaborator', $collaborator);
        $queryBuilder->setParameter('status_established', CampCollaboration::STATUS_ESTABLISHED);
    }
}
