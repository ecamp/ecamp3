<?php

namespace EcampWeb\Fieldset\EventCategory;

use EcampLib\Form\BaseCollection;
use Zend\Stdlib\ArrayUtils;

class EventCategoriesCollection extends BaseCollection
{
    public function __construct($name = null, $options = array())
    {
        $name = $name ?: 'event-categories-collection';
        $options = ArrayUtils::merge(
            array(
                'label' => '',
                'count' => 1,
                'allow_add' => true,
                'should_create_template' => true,
                'target_element' => array(
                    'type' => 'EcampCore\Fieldset\EventCategory\EventCategoryFieldset'
                )
            ),
            $options
        );

        parent::__construct($name, $options);
    }
}
