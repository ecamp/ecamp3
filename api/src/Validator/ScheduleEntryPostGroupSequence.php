<?php

declare(strict_types=1);

namespace App\Validator;

use ApiPlatform\Symfony\Validator\ValidationGroupsGeneratorInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;

class ScheduleEntryPostGroupSequence implements ValidationGroupsGeneratorInterface {
    #[\Override]
    public function __invoke($object): GroupSequence {
        return new GroupSequence(['validPeriod', 'Default', 'create']); // now, no matter which is first in the class declaration, it will be tested in this order.
    }
}
