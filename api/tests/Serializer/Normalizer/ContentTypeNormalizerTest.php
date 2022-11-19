<?php

namespace App\Tests\Serializer\Normalizer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\ContentType;
use App\Metadata\Resource\Factory\UriTemplateFactory;
use App\Serializer\Normalizer\ContentTypeNormalizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Rize\UriTemplate;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
class ContentTypeNormalizerTest extends TestCase {
    private ContentTypeNormalizer $normalizer;

    private MockObject|NormalizerInterface $decoratedMock;
    private MockObject|IriConverterInterface $iriConverter;
    private MockObject|UriTemplate $uriTemplate;
    private MockObject|UriTemplateFactory $uriTemplateFactory;

    protected function setUp(): void {
        $this->decoratedMock = $this->createMock(NormalizerInterface::class);

        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->uriTemplate = $this->createMock(UriTemplate::class);
        $this->uriTemplateFactory = $this->createMock(UriTemplateFactory::class);

        $this->normalizer = new ContentTypeNormalizer(
            $this->decoratedMock,
            $this->uriTemplate,
            $this->uriTemplateFactory,
            $this->iriConverter,
        );
        $this->normalizer->setSerializer($this->createMock(SerializerInterface::class));
    }

    public function testDelegatesSupportCheckToDecorated() {
        $this->decoratedMock
            ->expects($this->exactly(2))
            ->method('supportsNormalization')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->normalizer->supportsNormalization([]));
        $this->assertFalse($this->normalizer->supportsNormalization([]));
    }

    public function testDelegatesNormalizeToDecorated() {
        // given
        $resource = new \stdClass();
        $delegatedResult = [
            'hello' => 'world',
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;

        // when
        $result = $this->normalizer->normalize($resource, null, ['resource_class' => \stdClass::class]);

        // then
        $this->assertEquals($delegatedResult, $result);
    }

    public function testNormalizeAddsEntityPath() {
        // given
        $contentType = new ContentType();
        $contentType->entityClass = 'App\Entity\ContentNode\DummyContentNode';

        $delegatedResult = [
            'hello' => 'world',
        ];
        $this->decoratedMock->expects($this->once())
            ->method('normalize')
            ->willReturn($delegatedResult)
        ;
        $this->uriTemplateFactory->expects($this->once())
            ->method('createFromResourceClass')
            ->willReturn(['/templatedUri', 'true'])
        ;

        $this->uriTemplate->expects($this->once())
            ->method('expand')
            ->willReturn('/expandedUri')
        ;

        // when
        $result = $this->normalizer->normalize($contentType, null, ['resource_class' => ContentType::class]);

        // then
        $expectedResult = [
            'hello' => 'world',
            '_links' => [
                'contentNodes' => [
                    'href' => '/expandedUri',
                ],
            ],
        ];
        $this->assertEquals($expectedResult, $result);
    }
}
