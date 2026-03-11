<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Util\QueryBuilderHelper;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\TypeInfo\Type;

final class CampCollaboratorFilter extends AbstractFilter {
    public const string QUERY_PARAM_NAME = 'campCollaborator';

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
    public function getDescription(string $resourceClass): array {
        return [self::QUERY_PARAM_NAME => [
            'property' => self::QUERY_PARAM_NAME,
            'type' => Type::string()->__toString(),
            'required' => false,
        ]];
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
        if (!is_a($resourceClass, BelongsToCampInterface::class, true)) {
            throw new \Exception("CampCollaboratorFilter can only be applied to entities which implement BelongsToCampInterface (received: {$resourceClass}).");
        }

        if (self::QUERY_PARAM_NAME !== $property) {
            return;
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
