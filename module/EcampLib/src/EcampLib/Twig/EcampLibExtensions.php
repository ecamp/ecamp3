<?php

namespace EcampLib\Twig;

use Twig_Extension;

class EcampLibExtensions extends Twig_Extension
{
    public function getName()
    {
        return 'EcampLibExtensions';
    }

    public function getTokenParsers()
    {
        return array(
            new RenderViewModelTokenParser()
        );
    }
}
