<?php

namespace App\HttpCache;

interface CanGenerateTagsInterface {
    /**
     * Generates additional cache tags to be added to the response.
     *
     * @return string[] List of tags to add
     */
    public function getCacheTags(): array;
}
