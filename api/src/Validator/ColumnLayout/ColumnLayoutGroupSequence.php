<?php

namespace App\Validator\ColumnLayout;

use Symfony\Component\Validator\Constraints\GroupSequence;

class ColumnLayoutGroupSequence {
    public function __invoke() {
        return new GroupSequence(['columns_schema', 'Default']);
    }
}
