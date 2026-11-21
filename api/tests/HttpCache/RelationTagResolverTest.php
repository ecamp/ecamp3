<?php

declare(strict_types=1);

namespace App\Tests\HttpCache;

use App\HttpCache\RelationTagResolver;
use App\Tests\HttpCache\Entity\Dummy;
use App\Tests\HttpCache\Entity\NotAResource;
use App\Tests\HttpCache\Entity\RelatedDummy;
use App\Tests\HttpCache\Entity\RelatedOwningDummy;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RelationTagResolverTest extends TestCase {
    use CreatesRelationTagResolver;

    private RelationTagResolver $resolver;

    protected function setUp(): void {
        $this->resolver = $this->createRelationTagResolver();
    }

    public function testInverseSideOfToManyIsTagged(): void {
        $this->assertTrue($this->resolver->shouldTagProperty(RelatedDummy::class, 'dummies'));
    }

    public function testInverseSideOfOneToOneIsTagged(): void {
        $this->assertTrue($this->resolver->shouldTagProperty(RelatedOwningDummy::class, 'ownedDummy'));
    }

    public function testManyToManyIsTagged(): void {
        $this->assertTrue($this->resolver->shouldTagProperty(Dummy::class, 'manyToManyDummies'));
    }

    public function testOwningSideIsNotTagged(): void {
        $this->assertFalse($this->resolver->shouldTagProperty(Dummy::class, 'relatedOwningDummy'));
    }

    public function testRelationTagIsIdAndSerializedName(): void {
        $this->assertSame('42#manyToManyDummies', $this->resolver->createRelationTag('42', Dummy::class, 'embeddedManyToManyDummies'));
    }

    public function testNoRelationTagForOwningSide(): void {
        $this->assertNull($this->resolver->createRelationTag('42', Dummy::class, 'relatedDummy'));
    }

    public function testSerializedNameIsUsed(): void {
        // Dummy::$relatedDummy is serialized as 'relatedDummy' but is an owning side
        $this->assertFalse($this->resolver->shouldTagProperty(Dummy::class, 'relatedDummy'));
    }

    public function testUnknownRelationIsNotTagged(): void {
        $this->assertFalse($this->resolver->shouldTagProperty(Dummy::class, 'notAnAssociation'));
    }

    public function testTransientClassIsNotTagged(): void {
        $this->assertFalse($this->resolver->shouldTagProperty(NotAResource::class, 'dummies'));
    }
}
