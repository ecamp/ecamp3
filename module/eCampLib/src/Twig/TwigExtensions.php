<?php

namespace eCamp\Lib\Twig;

use Twig\TwigFunction;
use Twig\TwigTest;
use ZendTwig\Extension\Extension;

class TwigExtensions extends Extension
{
    public function getFunctions() {
        return [
            'class' => new TwigFunction('class', array($this, 'getClass'))
        ];
    }

    public function getClass($object) {
        return (new \ReflectionClass($object))->getShortName();
    }


    public function getTests() {
        return [
            new TwigTest('instanceof', [$this, 'isInstanceOf'])
        ];
    }

    public function isInstanceOf($var, $instance) {
        $reflexionClass = new \ReflectionClass($instance);
        return $reflexionClass->isInstance($var);
    }
}
