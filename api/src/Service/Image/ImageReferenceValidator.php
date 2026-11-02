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
            if (null === $camp || !preg_match('~^/(?:api/)?camps/([0-9a-f]{12,16})/images/([0-9a-f]{12,16})$~D', $image->getAttribute('src'), $match)
                || $match[1] !== $camp->getId()
                || null === $this->em->getRepository(CampImage::class)->findOneBy(['id' => $match[2], 'camp' => $camp])) {
                throw new UnprocessableEntityHttpException('Images must belong to this camp.');
            }
        }
    }
}
