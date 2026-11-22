<?php

namespace App\Service\Image;

use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentNode\Storyboard;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Dom\HTMLDocument;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class CampImageCopyService {
    public function __construct(
        private EntityManagerInterface $em,
        private CampImageReadAccess $readAccess,
    ) {}

    public function copy(EntityMap $entityMap): void {
        $targetCamp = $entityMap->getTargetCamp();
        $copied = [];
        foreach ($entityMap->getEntities() as $entity) {
            if (!$entity instanceof SingleText && !$entity instanceof Storyboard) {
                continue;
            }
            $html = implode("\n", HtmlImageReferences::htmlFromData($entity->data ?? []));
            if (!str_contains($html, '<img')) {
                continue;
            }

            $document = HTMLDocument::createFromString($html, LIBXML_NOERROR, 'UTF-8');
            foreach ($document->getElementsByTagName('img') as $image) {
                $hash = HtmlImageReferences::hashFromPath($image->getAttribute('src'));
                if (null === $hash) {
                    throw new UnprocessableEntityHttpException('Invalid camp image reference.');
                }
                if (isset($copied[$hash])) {
                    continue;
                }

                $source = $this->readAccess->find($hash);
                if (null === $source) {
                    throw new UnprocessableEntityHttpException('Camp image not found.');
                }

                $target = $source->camp->getId() === $targetCamp->getId() ? $source
                    : $this->em->getRepository(CampImage::class)->findOneBy(['camp' => $targetCamp, 'sha256' => $hash]);
                if (null === $target) {
                    $target = new CampImage();
                    $target->camp = $targetCamp;
                    $target->sha256 = $hash;
                    $target->mediaType = $source->mediaType;
                    $this->em->persist($target);
                }
                $target->unreferencedSince = null;
                $copied[$hash] = true;
            }
        }
    }
}
