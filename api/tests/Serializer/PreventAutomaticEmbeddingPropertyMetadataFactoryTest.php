<?php

namespace App\Tests\Serializer;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use ApiPlatform\Core\Metadata\Property\SubresourceMetadata;
use App\Serializer\PreventAutomaticEmbeddingPropertyMetadataFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

/**
 * @internal
 */
class PreventAutomaticEmbeddingPropertyMetadataFactoryTest extends TestCase {
    public function testCreateResetsReadableLinkAndWritableLinkToNull() {
        // given
        $decorated = $this->createMock(PropertyMetadataFactoryInterface::class);
        $propertyMetadata = new PropertyMetadata(
            $this->createMock(Type::class),
            'description',
            true,
            true,
            true,
            true,
            true,
            true,
            '/iri/of/entity',
            null,
            [],
            new SubresourceMetadata(Dummy::class, true, 3),
            true,
            null,
            null,
            []
        );
        $decorated->expects($this->once())
            ->method('create')
            ->willReturn($propertyMetadata)
        ;
        $factory = new PreventAutomaticEmbeddingPropertyMetadataFactory($decorated);

        // when
        $result = $factory->create(Dummy::class, 'myProperty', []);

        // then
        $this->assertEquals($propertyMetadata->getType(), $result->getType());
        $this->assertEquals($propertyMetadata->getDescription(), $result->getDescription());
        $this->assertEquals($propertyMetadata->isReadable(), $result->isReadable());
        $this->assertEquals($propertyMetadata->isWritable(), $result->isWritable());
        $this->assertEquals(null, $result->isReadableLink());
        $this->assertEquals(null, $result->isWritableLink());
        $this->assertEquals($propertyMetadata->isRequired(), $result->isRequired());
        $this->assertEquals($propertyMetadata->isIdentifier(), $result->isIdentifier());
        $this->assertEquals($propertyMetadata->getIri(), $result->getIri());
        $this->assertEquals($propertyMetadata->getAttributes(), $result->getAttributes());
        $this->assertEquals($propertyMetadata->getSubresource(), $result->getSubresource());
        $this->assertEquals($propertyMetadata->isInitializable(), $result->isInitializable());
        $this->assertEquals($propertyMetadata->getDefault(), $result->getDefault());
        $this->assertEquals($propertyMetadata->getExample(), $result->getExample());
        $this->assertEquals($propertyMetadata->getSchema(), $result->getSchema());
    }
}

class Dummy {
}
