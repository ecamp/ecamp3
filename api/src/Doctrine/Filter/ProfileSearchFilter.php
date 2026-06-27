<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\TypeInfo\Type;

/**
 * Allows to search profiles by a single search term which is matched (case-insensitively
 * and partially) against the firstname, surname, nickname and email at the same time.
 *
 * This is meant to power the autocomplete when inviting collaborators to a camp: a user can
 * type any part of a name or email and the matching profiles (which are already scoped to
 * people they share a camp with by {@see ProfileRepository::filterByUser})
 * are returned.
 */
final class ProfileSearchFilter extends AbstractFilter {
    public const string QUERY_PARAM_NAME = 'search';

    /**
     * The profile properties which are searched. They are backed by pg_trgm GIN indexes
     * (see migration Version20260627* / {@see Profile}) so the ILIKE comparisons can use an index.
     */
    private const array SEARCHED_PROPERTIES = ['firstname', 'surname', 'nickname', 'email'];

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
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

        // A search term that is not valid UTF-8 cannot be sent to PostgreSQL (it rejects the
        // bind parameter with "invalid byte sequence for encoding UTF8"). Reject such input with
        // a 400 Bad Request instead of letting it surface as a 500 server error.
        if (!mb_check_encoding($value, 'UTF-8')) {
            throw new BadRequestHttpException(sprintf('The "%s" query parameter must be a valid UTF-8 string.', self::QUERY_PARAM_NAME));
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName('search');

        $expr = $queryBuilder->expr();
        $orConditions = [];
        foreach (self::SEARCHED_PROPERTIES as $searchedProperty) {
            // LOWER(...) LIKE LOWER(...) keeps the comparison case-insensitive while staying
            // valid DQL (ILIKE is not). Thanks to the functional pg_trgm GIN indexes on
            // LOWER(<column>), this can be served by a bitmap index scan instead of a
            // sequential scan over all profiles.
            $orConditions[] = "LOWER({$rootAlias}.{$searchedProperty}) LIKE LOWER(:{$parameterName})";
        }

        $queryBuilder->andWhere($expr->orX(...$orConditions));
        $queryBuilder->setParameter($parameterName, '%'.$this->escapeLike($value).'%');
    }

    /**
     * Escapes the characters which have a special meaning in a LIKE/ILIKE pattern so that a
     * user searching e.g. for "50%" does not accidentally turn it into a wildcard.
     */
    private function escapeLike(string $value): string {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], trim($value));
    }
}
