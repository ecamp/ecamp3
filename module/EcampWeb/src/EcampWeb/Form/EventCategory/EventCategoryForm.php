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

    }
}