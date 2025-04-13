<?php

namespace App\Tests\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Serializer\TagCollectorInterface;
use App\HttpCache\ResponseTagger;
use App\HttpCache\TagCollector;
use App\Tests\HttpCache\Entity\Dummy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 */
class TagCollectorTest extends TestCase {
    use ProphecyTrait;

    private TagCollectorInterface $tagCollector;
    private ObjectProphecy $responseTaggerProphecy;
    private ObjectProphecy $em;
    private ObjectProphecy $classMetadata;

    protected function setUp(): void {
        // given
        $this->responseTaggerProphecy = $this->prophesize(ResponseTagger::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->classMetadata = $this->prophesize(ClassMetadata::class);
        $this->em->getClassMetadata(Argument::any())->willReturn($this->classMetadata);
        $this->classMetadata->getAssociationMappings(Argument::any())->willReturn([]);
        $this->tagCollector = new TagCollector($this->responseTaggerProphecy->reveal(), $this->em->reveal());
    }

    public function testNoTagForEmptyContext() {
        // then
        $this->responseTaggerProphecy->addTags(Argument::any())->shouldNotBeCalled();

        // when
        $this->tagCollector->collect([]);
    }

    public function testWithIri() {
        // then
        $this->responseTaggerProphecy->addTags(['/test-iri'])->shouldBeCalled();

        // when
        $this->tagCollector->collect(['iri' => '/test-iri']);
    }

    public function testWithBaseEntity() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTaggerProphecy->addTags(['123'])->shouldBeCalled();

        // when
        $this->tagCollector->collect(['iri' => '/dummy/123', 'object' => $object]);
    }

    public function testWithRelation() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTaggerProphecy->addTags(['123#propertyName'])->shouldBeCalled();

        // when
        $this->tagCollector->collect([
            'iri' => '/dummy/123',
            'object' => $object,
            'property_metadata' => new ApiProperty(),
            'api_attribute' => 'propertyName',
        ]);
    }

    public function testWithExtraCacheDependency() {
        // given
        $object = new Dummy();
        $object->setId('123');

        // then
        $this->responseTaggerProphecy->addTags(['123#PROPERTY_NAME'])->shouldBeCalled();
        $this->responseTaggerProphecy->addTags(['123#OTHER_DEPENDENCY'])->shouldBeCalled();

        // when
        $this->tagCollector->collect([
            'iri' => '/dummy/123',
            'object' => $object,
            'property_metadata' => new ApiProperty(
                extraProperties: [
                    'cacheDependencies' => ['PROPERTY_NAME', 'OTHER_DEPENDENCY'],
                ]
            ),
            'api_attribute' => 'propertyName',
        ]);
    }

    public function testNoTagForHalLinks() {
        // then
        $this->responseTaggerProphecy->addTags(Argument::any())->shouldNotBeCalled();

        // when
        $this->tagCollector->collect([
            'iri' => '/test-iri',
            'format' => 'jsonhal',
            'data' => '/test-iri',
        ]);
    }

    public function testNoTagForJsonLdLinks() {
        // then
        $this->responseTaggerProphecy->addTags(Argument::any())->shouldNotBeCalled();

        // when
        $this->tagCollector->collect([
            'iri' => '/test-iri',
            'format' => 'jsonld',
            'data' => '/test-iri',
        ]);
    }

    public function testNoTagForJsonApiLinks() {
        // then
        $this->responseTaggerProphecy->addTags(Argument::any())->shouldNotBeCalled();

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
