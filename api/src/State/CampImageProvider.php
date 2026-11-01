<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\CampImage;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProviderInterface<CampImage>
 */
final readonly class CampImageProvider implements ProviderInterface {
    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?CampImage {
        return $this->em->getRepository(CampImage::class)->findOneBy([
            'id' => $uriVariables['id'],
            'camp' => $uriVariables['campId'],
        ]);
    }
}
