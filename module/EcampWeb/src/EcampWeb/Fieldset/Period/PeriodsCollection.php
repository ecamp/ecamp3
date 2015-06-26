<?php

namespace EcampWeb\Fieldset\Period;

use EcampLib\Form\BaseCollection;
use Zend\Stdlib\ArrayUtils;

class PeriodsCollection extends BaseCollection
{
    public function __construct($name = null, $options = array())
    {
        $name = $name ?: 'periods-collection';
        $options = ArrayUtils::merge(
            array(
                'label' => '',
                'count' => 1,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'EcampCore\Fieldset\Period\PeriodFieldset'
                )
            ),
            $options
        );

        parent::__construct($name, $options);
    }
}
