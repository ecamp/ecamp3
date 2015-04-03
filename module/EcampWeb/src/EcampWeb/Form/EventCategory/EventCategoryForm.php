<?php

namespace EcampWeb\Form\EventCategory;

use EcampWeb\Form\AjaxBaseForm;

class EventCategoryForm extends AjaxBaseForm
{
    public function __construct($name = null)
    {
        $name = $name ?: 'event-category';
        parent::__construct($name);
    }

    public function init()
    {
        parent::init();

        $this->add(array(
            'name' => 'event-category',
            'type' => 'EcampCore\Fieldset\EventCategory\EventCategoryFieldset',
            'options' => array(
                'use_as_base_fieldset' => true
            )
        ));
    }

    public function setCampTypeId($campTypeId)
    {
        $this->get('event-category')->setCampTypeId($campTypeId);
    }

}
