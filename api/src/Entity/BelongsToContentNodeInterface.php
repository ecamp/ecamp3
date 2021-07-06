<?php

namespace App\Entity;

interface BelongsToContentNodeInterface {
    public function getContentNode(): ?ContentNode;
}
