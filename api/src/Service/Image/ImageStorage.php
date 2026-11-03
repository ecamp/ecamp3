<?php

namespace App\Service\Image;

use App\Entity\ImageBlob;

interface ImageStorage {
    public function store(ProcessedImage $image): void;

    public function delete(ImageBlob $image): void;

    /** @return array<string, string> */
    public function getDeliveryHeaders(ImageBlob $image, bool $head = false): array;
}
