<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormElement;

class FormElement extends TwbBundleFormElement
{
    public function render(\Zend\Form\ElementInterface $oElement)
    {
//        $oElement->setAttribute('data-form-element', null);
//        $oElement->setAttribute('data-feedback-icon', 'remove');

        //var_dump($oElement);

//        $markup = parent::render($oElement);

/*

        $feedback = $oElement->getOption('feedback-icon');
        if ($feedback) {
            $layout = $oElement->getOption('twb-layout');

            if ($layout == \TwbBundle\Form\View\Helper\TwbBundleForm::LAYOUT_HORIZONTAL) {
//                $markup .= '<i class="form-control-feedback glyphicon glyphicon-' . $feedback . '"
//                                data-container="body" data-toggle="popover"
//                                data-placement="right" data-content=""></i>';

//                $markup .= '
//                    <script type="text/javascript">
//                        $(function(){ $("i[data-toggle=popover]").popover(); });
//                    </script>';
            }
        }
*/

//        return
//            "<!-- start element rendering -->" .
//            "<div class='input-group'>" .
//                $markup .
//                "<span class='input-group-addon'>
//                    <i class='glyphicon glyphicon-remove'></i>
//                </span>" .
//            "</div>" .
//            "<!-- stop element rendering -->";

        $oElement->setAttribute('data-ng-class', 'elementClass');

        return "<twb-form-element>" . parent::render($oElement) . "</twb-form-element>";
    }

}
