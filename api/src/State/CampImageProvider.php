<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\CampImage;
use App\Service\Image\CampImageReadAccess;

/**
 * @implements ProviderInterface<CampImage>
 */
final readonly class CampImageProvider implements ProviderInterface {
    public function __construct(private CampImageReadAccess $readAccess) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?CampImage {
        return $this->readAccess->find($uriVariables['sha256']);
    }
}
