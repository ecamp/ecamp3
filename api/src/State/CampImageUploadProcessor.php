<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\CampImageUpload;
use App\Service\Image\ImageUploadService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @implements ProcessorInterface<CampImageUpload, CampImageUpload>
 */
final readonly class CampImageUploadProcessor implements ProcessorInterface {
    public function __construct(private ImageUploadService $uploader, private UrlGeneratorInterface $urlGenerator) {}

    /**
     * @param CampImageUpload $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CampImageUpload {
        $file = $context['request']->files->get('file');
        if (!$file instanceof UploadedFile) {
            throw new BadRequestHttpException('An image must be uploaded in the file field.');
        }

        $image = $this->uploader->upload($data->camp, $file);
        $blob = $image->imageBlob;
        $data->id = $image->getId();
        $data->src = $this->urlGenerator->generate('camp_image_download', ['campId' => $data->camp->getId(), 'id' => $image->getId()]);
        $data->mediaType = $blob->mediaType;
        $data->byteSize = $blob->byteSize;
        $data->width = $blob->width;
        $data->height = $blob->height;

        return $data;
    }
}
