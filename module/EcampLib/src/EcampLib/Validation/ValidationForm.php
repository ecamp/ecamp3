<?php

namespace EcampLib\Validation;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use EcampLib\Entity\BaseEntity;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ValidationForm extends Form
{
    protected $entity;
    protected $entityManager;
    protected $doctrineHydrator;

    public function __construct(EntityManager $entityManager, BaseEntity $entity)
    {
        parent::__construct('ValidationForm');

        $this->entity = $entity;
        $this->entityManager = $entityManager;
        $this->doctrineHydrator = new DoctrineHydrator($entityManager);

        $this->setHydrator($this->doctrineHydrator);
        $this->bind($entity);
    }

    public function addFieldset(Fieldset $fieldset, $baseFieldset = true)
    {
        $fieldset->setObject($this->entity);
        $fieldset->setHydrator($this->doctrineHydrator);
        $fieldset->setUseAsBaseFieldset($baseFieldset);

        parent::add($fieldset);

        return $this;
    }

    /**
     * @param  array               $data
     * @throws ValidationException
     */
    public function setAndValidate($data)
    {
        if (!$this->setData($data)->isValid()) {
               throw new ValidationException(
                "Form validation error", array('data' => $this->getMessages()));
        }
    }

}
