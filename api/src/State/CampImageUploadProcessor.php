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

        [$image, $processed] = $this->uploader->upload($data->camp, $file);
        $data->id = $image->getId();
        $data->src = $this->urlGenerator->generate('camp_image_download', ['sha256' => $image->sha256]);
        $data->mediaType = $image->mediaType;
        $data->byteSize = strlen($processed->data);
        $data->width = $processed->width;
        $data->height = $processed->height;

        return $data;
    }
}
