<?php

namespace EcampCore\Validation;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class EntityForm extends Form
{
    public function __construct($entityManager, $fieldset, $entity=null)
    {
        parent::__construct('entity-form');

        if ($entity) {
            $this->setHydrator(new DoctrineHydrator($entityManager));
            $fieldset->setObject($entity);
            $this->bind($entity);
        }

        $fieldset->useAsBaseFieldset(true);
        $this->add($fieldset);
    }

    public function setDataAndValidate($data)
    {
        $this->setData($data);

        return $this->isValid();
    }
}
