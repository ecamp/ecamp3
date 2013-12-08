<?php

namespace EcampLib\Validation;

use Zend\Form\Form;
use EcampLib\Entity\BaseEntity;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;

class ValidationForm extends Form
{
    protected $entity;
     protected $doctrineHydrator;

    public function __construct(ObjectManager $entityManager, BaseEntity $entity)
    {
        parent::__construct('ValidationForm');

        $this->entity = $entity;
        $this->doctrineHydrator = new DoctrineHydrator($entityManager);

        $this->setHydrator($this->doctrineHydrator);
        $this->bind($entity);
    }

    public function addFieldset(Fieldset $fieldset, $baseFieldset = false)
    {
        $fieldset->setObject($this->entity);
        $fieldset->setHydrator($this->doctrineHydrator);
        $fieldset->setUseAsBaseFieldset($baseFieldset);

        parent::add($fieldset);

        return $this;
    }

}
