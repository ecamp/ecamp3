<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Serializer\TagCollectorInterface;
use App\HttpCache\RelationTagResolver;
use App\HttpCache\ResponseTagger;
use App\HttpCache\TagCollector;
use App\Tests\HttpCache\Entity\Dummy;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class TagCollectorTest extends TestCase {
    private TagCollectorInterface $tagCollector;
    private MockObject&ResponseTagger $responseTagger;
    private MockObject&RelationTagResolver $relationTagResolver;

    protected function setUp(): void {
        // given
        $this->responseTagger = $this->createMock(ResponseTagger::class);
        $this->relationTagResolver = $this->createMock(RelationTagResolver::class);
        $this->relationTagResolver->method('createRelationTag')->willReturnCallback(
            fn (string $id, string $class, string $property) => $id.'#'.$property
        );
        $this->tagCollector = new TagCollector($this->responseTagger, $this->relationTagResolver);
    }

    public function testNoTagForEmptyContext() {
        // then
        $this->responseTagger
            ->expects($this->never())
            ->method('addTags')
        ;

        // when
        $this->tagCollector->collect([]);
    }

    public function testWithIri() {
        // then
        $this->responseTagger
            ->expects($this->once())
            ->method('addTags')
            ->with(['/test-iri'])
        ;

        // when
        $this->tagCollector->collect(['iri' => '/test-iri']);
    }

    public function testWithBaseEntity() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTagger
            ->expects(once())
            ->method('addTags')
            ->with(['123'])
        ;

        // when
        $this->tagCollector->collect(['iri' => '/dummy/123', 'object' => $object]);
    }

    public function testWithRelation() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTagger
            ->expects(once())
            ->method('addTags')
            ->with(['123#propertyName'])
        ;

        // when
        $this->tagCollector->collect([
            'iri' => '/dummy/123',
            'object' => $object,
            'property_metadata' => new ApiProperty(),
            'api_attribute' => 'propertyName',
        ]);
    }

    public function testNoTagForRelationNotTagged() {
        // given
        $object = new Dummy();
        $object->setId('123');
        $relationTagResolver = $this->createMock(RelationTagResolver::class);
        $relationTagResolver->expects($this->once())->method('createRelationTag')->with('123', Dummy::class, 'propertyName')->willReturn(null);
        $tagCollector = new TagCollector($this->responseTagger, $relationTagResolver);

        // then
        $this->responseTagger
            ->expects(never())
            ->method('addTags')
        ;

        // when
        $tagCollector->collect([
            'iri' => '/dummy/123',
            'object' => $object,
            'property_metadata' => new ApiProperty(),
            'api_attribute' => 'propertyName',
        ]);
    }

    public function testNoTagForHalLinks() {
        // then
        $this->responseTagger
            ->expects(never())
            ->method('addTags')
        ;

        // when
        $this->tagCollector->collect([
            'iri' => '/test-iri',
            'format' => 'jsonhal',
            'data' => '/test-iri',
        ]);
    }

    public function testNoTagForJsonLdLinks() {
        // then
        $this->responseTagger
            ->expects(never())
            ->method('addTags')
        ;

        // when
        $this->tagCollector->collect([
            'iri' => '/test-iri',
            'format' => 'jsonld',
            'data' => '/test-iri',
        ]);
    }

    public function testNoTagForJsonApiLinks() {
        // then
        $this->responseTagger
            ->expects(never())
            ->method('addTags')
        ;

        // when
        $this->tagCollector->collect([
            'iri' => '/test-iri',
            'format' => 'jsonapi',
            'data' => [
                'data' => [
                    'type' => 'dummy',
                    'id' => '/test-iri',
                ],
            ],
        ]);
    }
}
