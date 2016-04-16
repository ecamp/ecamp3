<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormRow;
use Zend\Form\ElementInterface;

class FormRow extends TwbBundleFormRow
{

    public function render(ElementInterface $oElement, $labelPosition = null)
    {
        return "<twb-form-row>" . parent::render($oElement, $labelPosition) . "</twb-form-row>";
    }

}
