<?php

declare(strict_types=1);

namespace App\Tests\HttpCache;

use App\HttpCache\RelationTagResolver;
use App\Tests\HttpCache\Entity\Dummy;
use App\Tests\HttpCache\Entity\NotAResource;
use App\Tests\HttpCache\Entity\RelatedDummy;
use App\Tests\HttpCache\Entity\RelatedOwningDummy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory as DoctrineClassMetadataFactory;
use Symfony\Bundle\MakerBundle\Doctrine\StaticReflectionService;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

/**
 * Creates a RelationTagResolver for the test entities, with associations mapped as in the entity attributes.
 */
trait CreatesRelationTagResolver {
    private function createRelationTagResolver(): RelationTagResolver {
        $dummyMetadata = new ClassMetadata(Dummy::class);
        $dummyMetadata->mapManyToOne(['fieldName' => 'relatedDummy', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyMetadata->mapOneToOne(['fieldName' => 'relatedOwningDummy', 'targetEntity' => RelatedOwningDummy::class, 'inversedBy' => 'ownedDummy']);
        $dummyMetadata->mapManyToMany(['fieldName' => 'manyToManyDummies', 'targetEntity' => RelatedDummy::class, 'inversedBy' => 'dummies']);
        $dummyMetadata->wakeupReflection(new StaticReflectionService());

        $relatedDummyMetadata = new ClassMetadata(RelatedDummy::class);
        $relatedDummyMetadata->mapOneToMany(['fieldName' => 'dummies', 'targetEntity' => Dummy::class, 'mappedBy' => 'relatedDummy']);
        $relatedDummyMetadata->wakeupReflection(new StaticReflectionService());

        $relatedOwningDummyMetadata = new ClassMetadata(RelatedOwningDummy::class);
        $relatedOwningDummyMetadata->mapOneToOne(['fieldName' => 'ownedDummy', 'targetEntity' => Dummy::class, 'mappedBy' => 'relatedOwningDummy']);
        $relatedOwningDummyMetadata->wakeupReflection(new StaticReflectionService());

        $metadataFactory = $this->createStub(DoctrineClassMetadataFactory::class);
        $metadataFactory->method('isTransient')->willReturnCallback(fn (string $class) => NotAResource::class === $class);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getMetadataFactory')->willReturn($metadataFactory);
        $em->method('getClassMetadata')->willReturnCallback(fn (string $class) => match ($class) {
            Dummy::class => $dummyMetadata,
            RelatedDummy::class => $relatedDummyMetadata,
            RelatedOwningDummy::class => $relatedOwningDummyMetadata,
        });

        return new RelationTagResolver(
            $em,
            new MetadataAwareNameConverter(new ClassMetadataFactory(new AttributeLoader()))
        );
    }
}
