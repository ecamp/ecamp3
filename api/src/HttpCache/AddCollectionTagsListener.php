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

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\State\UriVariablesResolverTrait;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\Symfony\Util\RequestAttributesExtractor;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class AddCollectionTagsListener {
    use OperationRequestInitiatorTrait;
    use UriVariablesResolverTrait;

    public function __construct(private readonly IriConverterInterface $iriConverter, ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory, private SymfonyResponseTagger $responseTagger) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
    }

    public function onKernelResponse(ResponseEvent $event): void {
        $request = $event->getRequest();
        $operation = $this->initializeOperation($request);

        if (!$attributes = RequestAttributesExtractor::extractAttributes($request)) {
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
