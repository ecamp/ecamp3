<?php

namespace App\Service\Image;

interface ImageStorage {
    public function store(ProcessedImage $image): void;
}
