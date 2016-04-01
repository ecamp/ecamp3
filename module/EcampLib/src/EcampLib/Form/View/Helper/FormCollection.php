<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormCollection;
use Zend\Form\ElementInterface;

class FormCollection extends TwbBundleFormCollection
{
    public function render(ElementInterface $oElement)
    {
        return "<twb-form-collection>" .  parent::render($oElement) . "</twb-form-collection>";
    }

}
