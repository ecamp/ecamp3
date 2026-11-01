<?php

namespace App\Service\Image;

use App\Entity\Camp;
use App\Entity\CampImage;
use App\Entity\ImageBlob;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploadService {
    public function __construct(
        private ImageProcessor $processor,
        private ImageStorage $storage,
        private EntityManagerInterface $em,
    ) {}

    public function upload(Camp $camp, UploadedFile $file): CampImage {
        $image = $this->processor->process($file);

        return $this->em->wrapInTransaction(function () use ($camp, $image): CampImage {
            $this->em->getConnection()->executeQuery(
                'SELECT pg_advisory_xact_lock(hashtext(:hash))',
                ['hash' => $image->sha256],
            );

            $blob = $this->em->getRepository(ImageBlob::class)->findOneBy(['sha256' => $image->sha256]);
            if (null === $blob) {
                $this->storage->store($image);
                $blob = new ImageBlob();
                $blob->sha256 = $image->sha256;
                $blob->mediaType = 'image/webp';
                $blob->byteSize = strlen($image->data);
                $blob->width = $image->width;
                $blob->height = $image->height;
                $this->em->persist($blob);
            }
            $blob->unreferencedSince = null;

            $campImage = $this->em->getRepository(CampImage::class)->findOneBy(['camp' => $camp, 'imageBlob' => $blob]);
            if (null === $campImage) {
                $campImage = new CampImage();
                $campImage->camp = $camp;
                $campImage->imageBlob = $blob;
                $this->em->persist($campImage);
            }
            $campImage->unreferencedSince = null;

            return $campImage;
        });
    }
}
