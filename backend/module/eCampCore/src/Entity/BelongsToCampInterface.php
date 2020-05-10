<?php

namespace eCamp\Core\Entity;

interface BelongsToCampInterface {
    /**
     * @return Camp
     */
    public function getCamp();
}
