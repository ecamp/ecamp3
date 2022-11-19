<?php

namespace App\Tests\Serializer;

// use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
// use ApiPlatform\Metadata\Property\PropertyMetadata;
// use ApiPlatform\Metadata\Property\SubresourceMetadata;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use App\Serializer\PreventAutomaticEmbeddingPropertyMetadataFactory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PreventAutomaticEmbeddingPropertyMetadataFactoryTest extends TestCase {
    public function testCreateResetsReadableLinkAndWritableLinkToNull() {
        // given
        $decorated = $this->createMock(PropertyMetadataFactoryInterface::class);
        $propertyMetadata = new ApiProperty(
            'description',
            true,
            true,
            true,
            true,
            true,
            true,
            'default',
            ['example'],
            'deprecationReason',
            true,
            true,
            [],
            [],
            [],
            true,
            true,
            'securityPostDenormalize',
            [],
            [],
            [],
            true,
            [],
            true,
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

        $this->assertEquals($propertyMetadata->getDescription(), $result->getDescription());
        $this->assertEquals($propertyMetadata->isReadable(), $result->isReadable());
        $this->assertEquals($propertyMetadata->isWritable(), $result->isWritable());

        $this->assertEquals(null, $result->isReadableLink());
        $this->assertEquals(null, $result->isWritableLink());

        $this->assertEquals($propertyMetadata->isRequired(), $result->isRequired());
        $this->assertEquals($propertyMetadata->isIdentifier(), $result->isIdentifier());
        $this->assertEquals($propertyMetadata->getDefault(), $result->getDefault());
        $this->assertEquals($propertyMetadata->getExample(), $result->getExample());
        $this->assertEquals($propertyMetadata->getDeprecationReason(), $result->getDeprecationReason());
        $this->assertEquals($propertyMetadata->isFetchable(), $result->isFetchable());
        $this->assertEquals($propertyMetadata->getFetchEager(), $result->getFetchEager());
        $this->assertEquals($propertyMetadata->getJsonldContext(), $result->getJsonldContext());
        $this->assertEquals($propertyMetadata->getOpenapiContext(), $result->getJsonldContext());
        $this->assertEquals($propertyMetadata->getJsonSchemaContext(), $result->getJsonSchemaContext());
        $this->assertEquals($propertyMetadata->getPush(), $result->getPush());
        $this->assertEquals($propertyMetadata->getSecurity(), $result->getSecurity());
        $this->assertEquals($propertyMetadata->getSecurityPostDenormalize(), $result->getSecurityPostDenormalize());
        $this->assertEquals($propertyMetadata->getTypes(), $result->getTypes());
        $this->assertEquals($propertyMetadata->getBuiltinTypes(), $result->getBuiltinTypes());
        $this->assertEquals($propertyMetadata->getSchema(), $result->getSchema());
        $this->assertEquals($propertyMetadata->isInitializable(), $result->isInitializable());
        $this->assertEquals($propertyMetadata->getIris(), $result->getIris());
        $this->assertEquals($propertyMetadata->getGenId(), $result->getGenId());
        $this->assertEquals($propertyMetadata->getExtraProperties(), $result->getExtraProperties());
    }
}

class Dummy {
}
