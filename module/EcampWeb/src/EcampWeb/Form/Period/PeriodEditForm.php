<?php

namespace EcampWeb\Form\Period;

use EcampWeb\Form\AjaxBaseForm;
use EcampCore\Validation\PeriodFieldset;

class PeriodEditForm extends AjaxBaseForm
{
    public function __construct($entity)
    {
        parent::__construct('period-edit-form');

        // add fieldset
        $fieldset = new PeriodFieldset();
        $fieldset->setUseAsBaseFieldset(true);

        $this->add($fieldset);
        $this->bind($entity);

    }
}
