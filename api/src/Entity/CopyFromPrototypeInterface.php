<?php

namespace App\Entity;

interface CopyFromPrototypeInterface {
    public function copyFromPrototype($prototype, $entityMap): void;
}
