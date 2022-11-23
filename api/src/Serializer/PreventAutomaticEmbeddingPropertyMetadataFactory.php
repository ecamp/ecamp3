<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;

/**
 * This is meant as a thin wrapper around
 * ApiPlatform\Metadata\Property\Factory\SerializerPropertyMetadataFactory.
 * That class implements the automatic embedding logic based on serialization groups,
 * described in https://api-platform.com/docs/core/serialization/#embedding-relations.
 *
 * However, we have our own system for serialization groups and embedding entities,
 * with general groups 'read', 'write', 'create', 'update' etc. instead of entity-
 * specific groups as they are used in the API platform docs ('book', 'book:update').
 * Therefore, we don't want relations to be automatically embedded as soon as there
 * is a property with a matching serialization group on the related entity.
 *
 * As an example, we don't want the author to be embedded in the book here:
 *
 * #[ApiResource(normalization_context: ['groups' => ['read']])]
 * class Book {
 *   #[Groups('read')]
 *   public ?Person $author = null;
 * }
 *
 * #[ApiResource(normalization_context: ['groups' => ['read']])]
 * class Person {
 *   #[Groups('read')]
 *   public string $name = '';
 * }
 *
 * To prevent the author from being embedded due to just the 'read' group, this
 * class should be inserted just around SerializerPropertyMetadataFactory in the
 * decorator chain. It will undo only the undesired changes to the property
 * metadata that SerializerPropertyMetadataFactory adds.
 *
 * Currently, the SerializerPropertyMetadataFactory has a decoration priority of
 * 30, so this class should be assigned a priority of 29.
 * https://github.com/api-platform/core/blob/main/src/Bridge/Symfony/Bundle/Resources/config/metadata/metadata.xml#L65
 */
final class PreventAutomaticEmbeddingPropertyMetadataFactory implements PropertyMetadataFactoryInterface {
    public function __construct(private PropertyMetadataFactoryInterface $decorated) {
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options = []): ApiProperty {
        $apiProperty = $this->decorated->create($resourceClass, $property, $options);

        return new ApiProperty(
            $apiProperty->getDescription(),
            $apiProperty->isReadable(),
            $apiProperty->isWritable(),
            null, // reset readableLink to null
            null, // reset writableLink to null
            $apiProperty->isRequired(),
            $apiProperty->isIdentifier(),
            $apiProperty->getDefault(),
            $apiProperty->getExample(),
            $apiProperty->getDeprecationReason(),
            $apiProperty->isFetchable(),
            $apiProperty->getFetchEager(),
            $apiProperty->getJsonldContext(),
            $apiProperty->getOpenapiContext(),
            $apiProperty->getJsonSchemaContext(),
            $apiProperty->getPush(),
            $apiProperty->getSecurity(),
            $apiProperty->getSecurityPostDenormalize(),
            $apiProperty->getTypes(),
            $apiProperty->getBuiltinTypes(),
            $apiProperty->getSchema(),
            $apiProperty->isInitializable(),
            $apiProperty->getIris(),
            $apiProperty->getGenId(),
            $apiProperty->getExtraProperties()
        );
    }
}
