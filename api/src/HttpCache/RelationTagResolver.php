<?php

declare(strict_types=1);

namespace App\HttpCache;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Decides which relations of an entity get a relation tag (<id>#<relation>), and builds the tag.
 *
 * A relation tag is only needed if the row holding the foreign key is not the tagged entity itself: a changed
 * foreign key already purges the entity owning it. That leaves the inverse side of an association, and both
 * sides of a many-to-many association.
 */
class RelationTagResolver {
    public const string IRI_RELATION_DELIMITER = '#';

    /** @var array<class-string, array<string, bool>> */
    private array $taggedRelationsByClass = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NameConverterInterface $nameConverter,
    ) {}

    /**
     * @param class-string $class
     * @param string       $property name of the property holding the relation
     */
    public function shouldTagProperty(string $class, string $property): bool {
        return $this->getTaggedRelations($class)[$this->nameConverter->normalize($property, $class)] ?? false;
    }

    /**
     * @param class-string $class
     * @param string       $property name of the property holding the relation
     *
     * @return null|string the tag for the relation of the entity with the given id, or null if the relation gets no tag
     */
    public function createRelationTag(string $id, string $class, string $property): ?string {
        if (!$this->shouldTagProperty($class, $property)) {
            return null;
        }

        return $id.self::IRI_RELATION_DELIMITER.$this->nameConverter->normalize($property, $class);
    }

    /**
     * @param class-string $class
     *
     * @return array<string, bool> serialized name => tagged
     */
    private function getTaggedRelations(string $class): array {
        if (isset($this->taggedRelationsByClass[$class])) {
            return $this->taggedRelationsByClass[$class];
        }

        $taggedRelations = [];
        if (!$this->em->getMetadataFactory()->isTransient($class)) {
            foreach ($this->em->getClassMetadata($class)->getAssociationMappings() as $property => $associationMapping) {
                $serializedName = $this->nameConverter->normalize($property, $class);
                $taggedRelations[$serializedName] = !$associationMapping['isOwningSide'] || ClassMetadata::MANY_TO_MANY === $associationMapping['type'];
            }
        }

        return $this->taggedRelationsByClass[$class] = $taggedRelations;
    }
}
