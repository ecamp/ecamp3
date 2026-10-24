<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Profile;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\TypeInfo\Type;

final class ProfileSearchFilter extends AbstractFilter {
    public const string QUERY_PARAM_NAME = 'search';

    private const array SEARCHED_PROPERTIES = ['firstname', 'surname', 'nickname', 'email'];

    public function getDescription(string $resourceClass): array {
        return [self::QUERY_PARAM_NAME => [
            'property' => self::QUERY_PARAM_NAME,
            'type' => Type::string()->__toString(),
            'required' => false,
            'description' => 'Search profiles by a part of their firstname, surname, nickname or email.',
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
        if (Profile::class !== $resourceClass) {
            throw new \Exception("ProfileSearchFilter can only be applied to the Profile entity (received: {$resourceClass}).");
        }

        if (self::QUERY_PARAM_NAME !== $property) {
            return;
        }

        if (!is_string($value) || '' === trim($value)) {
            return;
        }

        if (!mb_check_encoding($value, 'UTF-8')) {
            throw new BadRequestHttpException(sprintf('The "%s" query parameter must be a valid UTF-8 string.', self::QUERY_PARAM_NAME));
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('search');

        $expr = $queryBuilder->expr();
        $orConditions = [];
        foreach (self::SEARCHED_PROPERTIES as $searchedProperty) {
            $orConditions[] = "LOWER({$rootAlias}.{$searchedProperty}) LIKE LOWER(:{$parameterName})";
        }

        $queryBuilder->andWhere($expr->orX(...$orConditions));
        $queryBuilder->setParameter($parameterName, '%'.$this->escapeLike($value).'%');
    }

    private function escapeLike(string $value): string {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], trim($value));
    }
}
