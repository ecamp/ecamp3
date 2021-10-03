<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * This class modifies the API entrypoint when retrieved in HAL JSON format (/index.jsonhal) to include URI templates, and additionally
 * makes sure that the relation names of the _links are in plural rather than the default singular of API platform.
 */
class URITemplateNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface {
    public function __construct(
        private NormalizerInterface $decorated,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory,
        private UrlGeneratorInterface $urlGenerator,
        private OpenApiFactoryInterface $openApiFactory,
        private EnglishInflector $inflector
    ) {
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $links['self']['href'] = $this->urlGenerator->generate('api_entrypoint');
        $links['auth']['href'] = $this->urlGenerator->generate('index_auth');

        // retrieve all paths from openapi spec
        $openApi = $this->openApiFactory->__invoke($context ?? []);
        $paths = $openApi->getPaths()->getPaths();

        $collectedPaths = [];
        foreach ($paths as $pathName => $pathItem) {
            // only care about GET actions
            if (null !== $pathItem->getGet()) {
                // tags includes the resource shortname by default
                $resourceName = $pathItem->getGet()->getTags()[0];
                $parameters = $pathItem->getGet()->getParameters();
                $hasId = false;
                $queryParameters = [];
                // check if route has path parameters and collect query parameters
                foreach ($parameters as $parameter) {
                    if ('path' === $parameter->getIn()) {
                        $hasId = true;
                    }
                    if ('query' === $parameter->getIn()) {
                        array_push($queryParameters, $parameter->getName());
                    }
                }
                if ($hasId) {
                    $collectedPaths[$resourceName]['itemUrl'] = $pathName;
                    $collectedPaths[$resourceName]['itemQueryParams'] = $queryParameters;
                    // routes with path parameters are always templated
                    $collectedPaths[$resourceName]['templated'] = true;
                } else {
                    $collectedPaths[$resourceName]['collectionUrl'] = $pathName;
                    $collectedPaths[$resourceName]['collectionQueryParams'] = $queryParameters;
                    // if query parameters exists, set templated to true
                    if (count($queryParameters) > 0) {
                        $collectedPaths[$resourceName]['templated'] = true;
                    }
                }
            }
        }
        // merge item and collection routes
        foreach ($collectedPaths as $resourceName => $path) {
            $url = $path['itemUrl'] ?? $path['collectionUrl'];
            // replace '/{id}' with '{/id}'
            $fullPath = preg_replace('/\/{(.+?)}/', '{/$1}', $url);
            $queryParameters = array_merge($path['itemQueryParams'] ?? [], $path['collectionQueryParams'] ?? []);
            if (count($queryParameters) > 0) {
                $fullPath .= '{?'.join(',', $queryParameters).'}';
            }
            $collectedPaths[$resourceName]['href'] = $fullPath;
            if (isset($path['templated'])) {
                $collectedPaths[$resourceName]['templated'] = true;
            }
        }

        foreach ($object->getResourceNameCollection() as $resourceClass) {
            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);

            if (empty($resourceMetadata->getCollectionOperations())) {
                continue;
            }

            $shortName = $resourceMetadata->getShortName();

            // pluralize will never return an empty array
            $pluralName = $this->inflector->pluralize(lcfirst($shortName))[0];

            if (isset($collectedPaths[$shortName])) {
                $links[$pluralName]['href'] = $collectedPaths[$shortName]['href'];
                if (isset($collectedPaths[$shortName]['templated'])) {
                    $links[$pluralName]['templated'] = true;
                }
            }
        }

        return ['_links' => $links];
    }

    public function hasCacheableSupportsMethod(): bool {
        if (!$this->decorated instanceof CacheableSupportsMethodInterface) {
            return false;
        }

        return $this->decorated->hasCacheableSupportsMethod();
    }
}
