<?php

namespace App\Validator\ColumnLayout;

use ApiPlatform\Core\Bridge\Symfony\Validator\ValidationGroupsGeneratorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;

class ColumnLayoutPostGroupSequence implements ValidationGroupsGeneratorInterface {
    public function __invoke($object): GroupSequence {
        return new GroupSequence(['columns_schema', 'Default', 'create']);
    }
}
