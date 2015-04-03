<?php

namespace EcampWeb\Fieldset\Job;

use EcampLib\Form\BaseCollection;
use Zend\Stdlib\ArrayUtils;

class JobsCollection extends BaseCollection
{
    public function __construct($name = null, $options = array())
    {
        $name = $name ?: 'jobs-collection';
        $options = ArrayUtils::merge(
            array(
                'label' => '',
                'count' => 1,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'EcampCore\Fieldset\Job\JobFieldset'
                )
            ),
            $options
        );

        parent::__construct($name, $options);
    }
}
