<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Serializer\TagCollectorInterface;
use App\HttpCache\ResponseTagger;
use App\HttpCache\TagCollector;
use App\Tests\HttpCache\Entity\Dummy;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

use function PHPUnit\Framework\never;
use function PHPUnit\Framework\once;

/**
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
class TagCollectorTest extends TestCase {
    private TagCollectorInterface $tagCollector;
    private MockObject&ResponseTagger $responseTagger;

    protected function setUp(): void {
        // given
        $this->responseTagger = $this->createMock(ResponseTagger::class);
        $this->tagCollector = new TagCollector(
            $this->responseTagger,
            new MetadataAwareNameConverter(new ClassMetadataFactory(new AttributeLoader()))
        );
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

    public function testRelationTagUsesSerializedName() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTagger
            ->expects(once())
            ->method('addTags')
            ->with(['123#relatedDummy'])
        ;

        // when
        $this->tagCollector->collect([
            'iri' => '/dummy/123',
            'object' => $object,
            'property_metadata' => new ApiProperty(),
            'api_attribute' => 'embeddedRelatedDummy',
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
