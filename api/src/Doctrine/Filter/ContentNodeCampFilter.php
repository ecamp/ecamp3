<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Activity;
use App\Entity\ContentNode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\TypeInfo\Type;

final class ContentNodeCampFilter extends AbstractFilter {
    public const string CAMP_QUERY_NAME = 'camp';

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
        return ['camp' => [
            'property' => self::CAMP_QUERY_NAME,
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
        if (ContentNode::class !== $resourceClass && !is_subclass_of($resourceClass, ContentNode::class)) {
            throw new \Exception("ContentNodeCampFilter can only be applied to entities of type ContentNode (received: {$resourceClass}).");
        }

        if (self::CAMP_QUERY_NAME !== $property) {
            return;
        }

        // load camp from query parameter value
        $camp = $this->iriConverter->getResourceFromIri($value);

        // generate alias to avoid interference with other filters
        $campParameterName = $queryNameGenerator->generateParameterName($property);
        $activityJoinAlias = $queryNameGenerator->generateJoinAlias('activity');

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $rootQry = $queryBuilder->getEntityManager()->createQueryBuilder();
        $rootQry
            ->select("identity({$activityJoinAlias}.rootContentNode)")
            ->from(Activity::class, $activityJoinAlias)
            ->where($queryBuilder->expr()->eq("{$activityJoinAlias}.camp", ":{$campParameterName}"))
        ;

        $queryBuilder->andWhere($queryBuilder->expr()->in("{$rootAlias}.root", $rootQry->getDQL()));
        $queryBuilder->setParameter($campParameterName, $camp);
    }
}
