<?php
/**
 * Created by PhpStorm.
 * User: pirminmattmann
 * Date: 01.05.14
 * Time: 16:23
 */

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormRow;

class FormRow extends TwbBundleFormRow
{
    //private static $formGroupFormat = '<div data-form-row data-feedback-icon="%s"';

    public function render(\Zend\Form\ElementInterface $oElement)
    {
//        $feedbackIcon = ($oElement->getOption('feedback-icon')); // ? '1' : '0';
//
//        $markup = trim(parent::render($oElement));
//        $markup = preg_replace('/^<div/', sprintf(self::$formGroupFormat, $feedbackIcon), $markup);

//        return "<!-- start FormRow rendering -->" . $markup . "<!-- end FormRow rendering -->";
        return "<twb-form-row>" . parent::render($oElement) . "</twb-form-row>";
    }

}
