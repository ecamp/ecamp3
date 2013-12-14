<?php

namespace EcampWeb\Form\Period;

use EcampWeb\Form\AjaxBaseForm;
use EcampCore\Validation\PeriodFieldset;

class PeriodCreateForm extends AjaxBaseForm
{
    public function __construct()
    {
        parent::__construct('period-form');

        // add period fieldset
        $periodFieldset = new PeriodFieldset();
        $periodFieldset->remove('moveEvents');

        $periodFieldset->setUseAsBaseFieldset(true);
        $this->add($periodFieldset);
    }
}
