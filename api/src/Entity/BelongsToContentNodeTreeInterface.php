<?php

namespace App\Entity;

use App\Entity\ContentNode\ColumnLayout;

interface BelongsToContentNodeTreeInterface {
    public function getRoot(): ?ColumnLayout;
}
