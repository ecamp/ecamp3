<?php

namespace eCamp\Core\Entity;

interface BelongsToContentNodeInterface {
    public function getContentNode(): ?ContentNode;
}
