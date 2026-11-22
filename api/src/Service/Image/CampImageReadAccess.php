<?php

namespace App\Service\Image;

use App\Entity\CampImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class CampImageReadAccess {
    public function __construct(private EntityManagerInterface $em, private Security $security) {}

    public function find(string $sha256): ?CampImage {
        $images = $this->em->getRepository(CampImage::class)->findBy(['sha256' => $sha256]);
        foreach ($images as $image) {
            if ($this->security->isGranted('CAMP_COLLABORATOR', $image) || $this->security->isGranted('CAMP_IS_PUBLIC', $image)) {
                return $image;
            }
        }
        if ([] !== $images) {
            throw new AccessDeniedException('Access to the image is denied.');
        }

        return null;
    }
}
