<?php

namespace eCamp\Lib\Types;

class DateTimeUtc extends UtcBase {
    protected string $FORMAT = 'Y-m-d\TH:iP';

    public function getDefaultTimeString(): string {
        return 'now';
    }
}
