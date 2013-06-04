<?php

namespace EcampLib\View\Helper;

use Zend\View\Helper\AbstractHelper;
use ArrayObject;

class ShowVariables
    extends AbstractHelper
{

    public function __invoke($renderer)
    {
        if (method_exists($renderer, 'vars')) {
            echo "<pre>";
            echo $this->toString($renderer->vars());
            echo "</pre>";
        }
    }

    private function toString($value, $dept = 0)
    {
        if (is_array($value) || $value instanceof ArrayObject) {
            foreach ($value as $k => $v) {
                echo str_repeat("   ", $dept) . $k . ":" . PHP_EOL;
                //echo str_repeat("   ", $dept). "{" . PHP_EOL;
                $this->toString($v, $dept + 1);
                //echo str_repeat("   ", $dept) . "}" . PHP_EOL;
            }
        } elseif (is_object($value)) {
            echo str_repeat("   ", $dept) . get_class($value) . PHP_EOL;
        } else {
            echo str_repeat("   ", $dept) . $value . PHP_EOL;
        }
    }

}
