<?php

namespace eCamp\Lib\Types;

class DateUtc extends UtcBase {
    protected string $FORMAT = 'Y-m-d';

    public function getDefaultTimeString(): string {
        return 'today';
    }
}
