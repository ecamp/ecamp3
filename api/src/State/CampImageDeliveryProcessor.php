<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\CampImage;
use App\Service\Image\ImageDelivery;
use Symfony\Component\HttpFoundation\Response;

/**
 * @implements ProcessorInterface<CampImage, Response>
 */
final readonly class CampImageDeliveryProcessor implements ProcessorInterface {
    public function __construct(private ImageDelivery $delivery) {}

    /**
     * @param CampImage $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Response {
        $response = new Response('', 200, ['Content-Type' => $data->mediaType, 'Cache-Control' => 'private, no-cache']);
        $response->setEtag($data->sha256);
        if ($response->isNotModified($context['request'])) {
            return $response;
        }

        $response->headers->add($this->delivery->getDeliveryHeaders($data, $context['request']->isMethod('HEAD')));

        return $response;
    }
}
