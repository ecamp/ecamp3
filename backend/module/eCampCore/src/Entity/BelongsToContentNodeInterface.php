<?php

namespace eCamp\Core\Entity;

interface BelongsToContentNodeTreeInterface {
    public function getContentNode(): ?ContentNode;
}
