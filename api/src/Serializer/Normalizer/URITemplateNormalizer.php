<?php

namespace App\Serializer\Normalizer;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This class modifies the API platform HAL EntrypointNormalizer to generate URI templates`.
 */
class URITemplateNormalizer implements NormalizerInterface, NormalizerAwareInterface {
    private NormalizerInterface $decorated;

    private $resourceMetadataFactory;

    public function __construct(NormalizerInterface $decorated, ResourceMetadataFactoryInterface $resourceMetadataFactory, OpenApiFactoryInterface $openApiFactory) {
        $this->decorated = $decorated;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->openApiFactory = $openApiFactory;
    }

    public function supportsNormalization($data, $format = null) {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = []) {
        $data = $this->decorated->normalize($object, $format, $context);
        $links['self'] = $data['_links']['self'];

        // retrieve all paths from openapi spec
        $openApi = $this->openApiFactory->__invoke($context ?? []);
        $paths = $openApi->getPaths()->getPaths();

        $collectedPaths = [];
        foreach ($paths as $pathName => $pathItem) {
            // only care about GET actions
            if (null !== $pathItem->getGet()) {
                $resourceName = $this->snakeToCamel(explode('/', $pathName)[1]);
                $parameters = $pathItem->getGet()->getParameters();
                $hasId = false;
                $index = 0;
                $queryParameters = [];
                // check if route has path parameters and collect query parameters
                while ($index < count($parameters)) {
                    if ('path' === $parameters[$index]->getIn()) {
                        $hasId = true;
                    }
                    if ('query' === $parameters[$index]->getIn()) {
                        array_push($queryParameters, $parameters[$index]->getName());
                    }
                    ++$index;
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
            $url = isset($path['itemUrl']) ? $path['itemUrl'] : $path['collectionUrl'];
            // replace '/{id}' with '{/id}'
            $fullPath = preg_replace('/\/{(.+?)}/', '{/$1}', $url);
            $queryParameters = array_merge($path['itemQueryParams'] ?? [], $path['collectionQueryParams'] ?? []);
            if (count($queryParameters) > 0) {
                $fullPath .= '{?'.join(',', $queryParameters).'}';
            }
            $links[$resourceName]['href'] = $fullPath;
            if (isset($path['templated'])) {
                $links[$resourceName]['templated'] = true;
            }
        }

        return ['_links' => $links];
    }

    public function setNormalizer(NormalizerInterface $normalizer) {
        if ($this->decorated instanceof NormalizerAwareInterface) {
            $this->decorated->setNormalizer($normalizer);
        }
    }

    private function snakeToCamel($input) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }
}
