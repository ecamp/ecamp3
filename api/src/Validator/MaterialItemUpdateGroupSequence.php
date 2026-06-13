<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Symfony\Validator\ValidationGroupsGeneratorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;

class MaterialItemUpdateGroupSequence implements ValidationGroupsGeneratorInterface {
    #[\Override]
    public function __invoke($object): GroupSequence {
        return new GroupSequence(['update', 'Default']); // now, no matter which is first in the class declaration, it will be tested in this order.
    }
}
