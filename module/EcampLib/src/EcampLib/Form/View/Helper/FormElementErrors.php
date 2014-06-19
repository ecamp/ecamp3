<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormElementErrors;
use Zend\Form\ElementInterface;

class FormElementErrors extends TwbBundleFormElementErrors
{
    public function render(ElementInterface $oElement, array $attributes = array())
    {
        $markup = parent::render($oElement, $attributes);

        return "<twb-form-element-errors>" . $markup . "</twb-form-element-errors>";
    }

}
