<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormElement;

class FormElement extends TwbBundleFormElement
{
    public function render(\Zend\Form\ElementInterface $oElement)
    {
        return "<twb-form-element>" . parent::render($oElement) . "</twb-form-element>";
    }

}
