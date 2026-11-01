<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampImage;
use App\Service\Image\ImageStorage;
use Symfony\Component\HttpFoundation\Response;

/**
 * @implements ProcessorInterface<CampImage, Response>
 */
final readonly class CampImageDeliveryProcessor implements ProcessorInterface {
    public function __construct(private ImageStorage $storage) {}

    /**
     * @param CampImage $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Response {
        $blob = $data->imageBlob;
        $response = new Response('', 200, ['Content-Type' => $blob->mediaType, 'Cache-Control' => 'private, no-cache']);
        $response->setEtag($blob->sha256);
        if ($response->isNotModified($context['request'])) {
            return $response;
        }

        $response->headers->add($this->storage->getDeliveryHeaders($blob, $context['request']->isMethod('HEAD')));

        return $response;
    }
}
