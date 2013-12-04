<?php

namespace EcampWeb\Form;

use Zend\Form\Form;
use EcampCore\Validation\PeriodFieldset;

class PeriodEditForm extends BaseForm
{
    public function __construct($entity)
    {
        parent::__construct('period-edit-form');

        // add fieldset
        $fieldset = new PeriodFieldset(null);
        $fieldset->setUseAsBaseFieldset(true);

        $fieldset->add(array(
                'name' => 'id',
                'type'  => 'Hidden',
        ));

        $this->add($fieldset);
        $this->setHydrator(new ClassMethodsHydrator());
        $this->bind($entity);
    }
}
