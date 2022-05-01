<?php

namespace App\Entity;

use App\Entity\ContentNode\ColumnLayout;

interface BelongsToContentNodeInterface {
    public function getRoot(): ?ColumnLayout;
}
