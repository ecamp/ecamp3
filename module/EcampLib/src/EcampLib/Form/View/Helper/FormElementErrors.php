<?php

namespace EcampLib\Form\View\Helper;

use TwbBundle\Form\View\Helper\TwbBundleFormElementErrors;
use Zend\Form\ElementInterface;

class FormElementErrors extends TwbBundleFormElementErrors
{
    public function render(ElementInterface $oElement, array $attributes = array())
    {
        $markup = parent::render($oElement, $attributes);

        /*
        $feedback = $oElement->getOption('feedback-icon');
        if ($feedback) {

            $layout = $oElement->getOption('twb-layout');

            if ($layout == \TwbBundle\Form\View\Helper\TwbBundleForm::LAYOUT_HORIZONTAL) {
                $markup .= '<i class="form-control-feedback glyphicon glyphicon-' . $feedback . '"
                                data-container="body" data-toggle="popover"
                                data-placement="right" data-content=""></i>';

                $markup .= '
                    <script type="text/javascript">
                        $(function(){ $("i[data-toggle=popover]").popover(); });
                    </script>
                ';
            }
        } else {

        }
        */

        return "<twb-form-element-errors>" . $markup . "</twb-form-element-errors>";
    }

}
