<?php

namespace App\Entity;

interface BelongsToCampInterface {
    public function getCamp(): ?Camp;
}
