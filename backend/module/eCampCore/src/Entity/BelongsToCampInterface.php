<?php

namespace eCamp\Core\Entity;

interface BelongsToCampInterface {
    public function getCamp(): ?Camp;
}
