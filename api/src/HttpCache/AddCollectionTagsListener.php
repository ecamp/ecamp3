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

namespace App\HttpCache;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use ApiPlatform\Util\RequestAttributesExtractor;
use ApiPlatform\Util\OperationRequestInitiatorTrait;
use ApiPlatform\State\UriVariablesResolverTrait;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Api\IriConverterInterface;

final class AddCollectionTagsListener
{
    use OperationRequestInitiatorTrait;
    use UriVariablesResolverTrait;

    public function __construct(private readonly IriConverterInterface $iriConverter, ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory, private SymfonyResponseTagger $responseTagger)
    {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $operation = $this->initializeOperation($request);

        if ( !$attributes = RequestAttributesExtractor::extractAttributes($request)) {
            return;
        }

        if ($operation instanceof CollectionOperationInterface) {
            // Allows to purge collections
            $uriVariables = $this->getOperationUriVariables($operation, $request->attributes->all(), $attributes['resource_class']);
            $iri = $this->iriConverter->getIriFromResource($attributes['resource_class'], UrlGeneratorInterface::ABS_PATH, $operation, ['uri_variables' => $uriVariables]);

            $this->responseTagger->addTags([$iri]);
        }

     
    }
}
