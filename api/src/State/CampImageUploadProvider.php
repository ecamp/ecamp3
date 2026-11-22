<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\DTO\CampImageUpload;
use App\Entity\Camp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProviderInterface<CampImageUpload>
 */
final readonly class CampImageUploadProvider implements ProviderInterface {
    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CampImageUpload {
        $campId = $context['request']->request->get('campId');
        if (!is_string($campId) || !preg_match('/\A[0-9a-f]{12,16}\z/', $campId)) {
            throw new BadRequestHttpException('A valid camp ID is required in the campId field.');
        }

        $camp = $this->em->find(Camp::class, $campId);
        if (null === $camp) {
            throw new NotFoundHttpException();
        }

        $upload = new CampImageUpload();
        $upload->camp = $camp;

        return $upload;
    }
}
