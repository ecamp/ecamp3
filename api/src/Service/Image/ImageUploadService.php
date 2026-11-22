<?php

namespace App\Service\Image;

use App\Entity\Camp;
use App\Entity\CampImage;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class ImageUploadService {
    public function __construct(
        private ImageProcessor $processor,
        private FilesystemOperator $storage,
        private EntityManagerInterface $em,
    ) {}

    /** @return array{CampImage, ProcessedImage} */
    public function upload(Camp $camp, UploadedFile $file): array {
        $image = $this->processor->process($file);

        $campImage = $this->em->wrapInTransaction(function () use ($camp, $image): CampImage {
            $repository = $this->em->getRepository(CampImage::class);
            $campImage = $repository->findOneBy(['camp' => $camp, 'sha256' => $image->sha256]);
            if (null === $campImage) {
                if (0 === $repository->count(['sha256' => $image->sha256])) {
                    $this->storage->write($image->getStorageKey(), $image->data, ['mimetype' => ProcessedImage::MEDIA_TYPE]);
                }
                $campImage = new CampImage();
                $campImage->camp = $camp;
                $campImage->sha256 = $image->sha256;
                $campImage->mediaType = ProcessedImage::MEDIA_TYPE;
                $this->em->persist($campImage);
            } else {
                $campImage->unreferencedSince = null;
            }

            return $campImage;
        });

        return [$campImage, $image];
    }
}
