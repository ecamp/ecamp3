<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\GroupSequence;

class MaterialItemUpdateGroupSequence {
    public function __invoke() {
        return new GroupSequence(['materialItem:update', 'Default']); // now, no matter which is first in the class declaration, it will be tested in this order.
    }
}
