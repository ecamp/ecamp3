<?php

namespace App\Entity;

interface HasParentInterface extends HasId {
    public function getParent(): ?HasParentInterface;
}
