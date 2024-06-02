<?php

/*
 * This file was partially copied from api-platform/core.
 *
 * For original author and license information see upstream file.
 *
 * Upstream file (main branch):               https://github.com/api-platform/core/blob/main/src/Symfony/EventListener/AddTagsListener.php
 * Upstream file (last synchronized version): https://github.com/api-platform/core/blob/66e26729540c91a44730cde75f3272fa94db6572/src/Symfony/EventListener/AddTagsListener.php
 * Last synchronized commit:                  2024-02-03 / 66e26729540c91a44730cde75f3272fa94db6572
 */

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\State\UriVariablesResolverTrait;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class AddCollectionTagsListener {
    use OperationRequestInitiatorTrait;
    use UriVariablesResolverTrait;

    public function __construct(private readonly IriConverterInterface $iriConverter, ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory, private ResponseTagger $responseTagger) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $operation = $this->initializeOperation($request);

        if (
            (!$attributes = RequestAttributesExtractor::extractAttributes($request))
            || $request->attributes->get('_api_platform_disable_listeners')
        ) {
            return;
        }

        if ($operation instanceof CollectionOperationInterface) {
            // Allows to purge collections
            $uriVariables = $this->getOperationUriVariables($operation, $request->attributes->all(), $attributes['resource_class']);
            $iri = $this->iriConverter->getIriFromResource($attributes['resource_class'], UrlGeneratorInterface::ABS_PATH, $operation, ['uri_variables' => $uriVariables]);

            if (!$iri) {
                return;
            }

            $this->responseTagger->addTags([$iri]);
        }
    }
}
