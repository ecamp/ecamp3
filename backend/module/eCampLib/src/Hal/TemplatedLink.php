<?php

namespace eCamp\Lib\Hal;

use Laminas\ApiTools\Hal\Link\Link;

class TemplatedLink extends Link {
    public function isTemplated(): bool {
        return true;
    }
}
