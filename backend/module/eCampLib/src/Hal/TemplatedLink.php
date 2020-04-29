<?php

namespace eCamp\Lib\Hal;

use ZF\Hal\Link\Link;

class TemplatedLink extends Link {
    public function isTemplated() {
        return true;
    }
}
