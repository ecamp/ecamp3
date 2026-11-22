<?php

namespace App\Service\Image;

use App\Entity\Camp;
use App\Entity\CampImage;
use Doctrine\ORM\EntityManagerInterface;
use Dom\HTMLDocument;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class ImageReferenceValidator {
    public function __construct(private EntityManagerInterface $em) {}

    public function validate(string $html, ?Camp $camp): void {
        if (!str_contains($html, '<img')) {
            return;
        }
        $document = HTMLDocument::createFromString($html, LIBXML_NOERROR, 'UTF-8');
        foreach ($document->getElementsByTagName('img') as $image) {
            $hash = HtmlImageReferences::hashFromPath($image->getAttribute('src'));
            if (null === $camp || null === $hash) {
                throw new UnprocessableEntityHttpException('Images must belong to this camp.');
            }
            $campImage = $this->em->getRepository(CampImage::class)->findOneBy(['sha256' => $hash, 'camp' => $camp]);
            if (null === $campImage) {
                throw new UnprocessableEntityHttpException('Images must belong to this camp.');
            }
            $campImage->unreferencedSince = null;
        }
    }
}
