<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormElement;
use TwbBundle\Options\ModuleOptions;

class FormElement extends TwbBundleFormElement
{
    public function __construct(ModuleOptions $options)
    {
        parent::__construct($options);
    }

    public function render(\Zend\Form\ElementInterface $oElement)
    {
        return "<twb-form-element>" . parent::render($oElement) . "</twb-form-element>";
    }

}
