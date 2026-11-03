<?php

namespace App\Service\Image;

use App\Entity\CampImage;
use App\Entity\ContentNode\SingleText;
use App\Util\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Dom\HTMLDocument;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class CampImageCopyService {
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function copy(EntityMap $entityMap): void {
        $targetCamp = $entityMap->getTargetCamp();
        $images = [];
        foreach ($entityMap->getEntities() as $entity) {
            if (!$entity instanceof SingleText || !str_contains($entity->data['html'], '<img')) {
                continue;
            }

            $document = HTMLDocument::createFromString($entity->data['html'], LIBXML_NOERROR, 'UTF-8');
            $changed = false;
            foreach ($document->getElementsByTagName('img') as $image) {
                if (!preg_match('~^/(?:api/)?camps/([0-9a-f]{12,16})/images/([0-9a-f]{12,16})$~D', $image->getAttribute('src'), $match)) {
                    throw new UnprocessableEntityHttpException('Invalid camp image reference.');
                }
                $source = $this->em->getRepository(CampImage::class)->findOneBy(['id' => $match[2], 'camp' => $match[1]]);
                if (null === $source) {
                    throw new UnprocessableEntityHttpException('Camp image not found.');
                }
                if (!$this->security->isGranted('CAMP_COLLABORATOR', $source) && !$this->security->isGranted('CAMP_IS_PUBLIC', $source)) {
                    throw new AccessDeniedHttpException('Access to the source image is denied.');
                }
                if ($source->camp->getId() === $targetCamp->getId()) {
                    continue;
                }

                $blob = $source->imageBlob;
                $target = $images[$blob->getId()] ?? $this->em->getRepository(CampImage::class)->findOneBy(['camp' => $targetCamp, 'imageBlob' => $blob]);
                if (null === $target) {
                    $target = new CampImage();
                    $target->camp = $targetCamp;
                    $target->imageBlob = $blob;
                    $this->em->persist($target);
                }
                $target->unreferencedSince = null;
                $blob->unreferencedSince = null;
                $images[$blob->getId()] = $target;
                $image->setAttribute('src', $this->urlGenerator->generate('camp_image_download', ['campId' => $targetCamp->getId(), 'id' => $target->getId()]));
                $changed = true;
            }
            if ($changed) {
                $html = '';
                foreach ($document->body->childNodes as $child) {
                    $html .= $document->saveHtml($child);
                }
                $entity->data['html'] = $html;
            }
        }
    }
}
