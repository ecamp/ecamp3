<?php

namespace App\Tests\Serializer;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use App\Serializer\PreventAutomaticEmbeddingPropertyMetadataFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type as PropertyInfoType;

/**
 * @internal
 */
class PreventAutomaticEmbeddingPropertyMetadataFactoryTest extends TestCase {
    public function testCreateResetsReadableLinkAndWritableLinkToNull() {
        // given
        $decorated = $this->createMock(PropertyMetadataFactoryInterface::class);
        $apiProperty = new ApiProperty(
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
            ['jsonldContext'],
            ['openapiContext'],
            ['jsonSchemaContext'],
            true,
            true,
            'securityPostDenormalize',
            ['types'],
            [new PropertyInfoType(builtinType: PropertyInfoType::BUILTIN_TYPE_INT)],
            ['schema'],
            true,
            ['iris'],
            true,
            'uriTemplate',
            'property',
            ['extraProperties']
        );
        $decorated->expects($this->once())
            ->method('create')
            ->willReturn($apiProperty)
        ;
        $factory = new PreventAutomaticEmbeddingPropertyMetadataFactory($decorated);

        // when
        $result = $factory->create(Dummy::class, 'myProperty', []);

        // then

        $this->assertEquals($apiProperty->getDescription(), $result->getDescription());
        $this->assertEquals($apiProperty->isReadable(), $result->isReadable());
        $this->assertEquals($apiProperty->isWritable(), $result->isWritable());

        $this->assertEquals(null, $result->isReadableLink());
        $this->assertEquals(null, $result->isWritableLink());

        $this->assertEquals($apiProperty->isRequired(), $result->isRequired());
        $this->assertEquals($apiProperty->isIdentifier(), $result->isIdentifier());
        $this->assertEquals($apiProperty->getDefault(), $result->getDefault());
        $this->assertEquals($apiProperty->getExample(), $result->getExample());
        $this->assertEquals($apiProperty->getDeprecationReason(), $result->getDeprecationReason());
        $this->assertEquals($apiProperty->isFetchable(), $result->isFetchable());
        $this->assertEquals($apiProperty->getFetchEager(), $result->getFetchEager());
        $this->assertEquals($apiProperty->getJsonldContext(), $result->getJsonldContext());
        $this->assertEquals($apiProperty->getOpenapiContext(), $result->getOpenapiContext());
        $this->assertEquals($apiProperty->getJsonSchemaContext(), $result->getJsonSchemaContext());
        $this->assertEquals($apiProperty->getPush(), $result->getPush());
        $this->assertEquals($apiProperty->getSecurity(), $result->getSecurity());
        $this->assertEquals($apiProperty->getSecurityPostDenormalize(), $result->getSecurityPostDenormalize());
        $this->assertEquals($apiProperty->getTypes(), $result->getTypes());
        $this->assertEquals($apiProperty->getBuiltinTypes(), $result->getBuiltinTypes());
        $this->assertEquals($apiProperty->getSchema(), $result->getSchema());
        $this->assertEquals($apiProperty->isInitializable(), $result->isInitializable());
        $this->assertEquals($apiProperty->getIris(), $result->getIris());
        $this->assertEquals($apiProperty->getGenId(), $result->getGenId());
        $this->assertEquals($apiProperty->getUriTemplate(), $result->getUriTemplate());
        $this->assertEquals($apiProperty->getProperty(), $result->getProperty());
        $this->assertEquals($apiProperty->getExtraProperties(), $result->getExtraProperties());
    }
}

class Dummy {}
