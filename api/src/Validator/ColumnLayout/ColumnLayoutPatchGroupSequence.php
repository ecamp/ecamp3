<?php

namespace App\Validator\ColumnLayout;

use ApiPlatform\Core\Bridge\Symfony\Validator\ValidationGroupsGeneratorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;

class ColumnLayoutPatchGroupSequence implements ValidationGroupsGeneratorInterface {
    public function __invoke($object) {
        return new GroupSequence(['columns_schema', 'Default', 'update']);
    }
}
