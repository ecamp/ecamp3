<?php

namespace App\Validator;

use ApiPlatform\Core\Bridge\Symfony\Validator\ValidationGroupsGeneratorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;

class MaterialItemUpdateGroupSequence implements ValidationGroupsGeneratorInterface {
    public function __invoke($object) {
        return new GroupSequence(['materialItem:update', 'Default']); // now, no matter which is first in the class declaration, it will be tested in this order.
    }
}
