<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\CampImageUpload;
use App\Entity\Camp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<CampImageUpload>
 */
final readonly class CampImageUploadProvider implements ProviderInterface {
    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CampImageUpload {
        $camp = $this->em->find(Camp::class, $uriVariables['campId']);
        if (null === $camp) {
            throw new NotFoundHttpException();
        }

        $upload = new CampImageUpload();
        $upload->camp = $camp;

        return $upload;
    }
}
