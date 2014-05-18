<?php

namespace EcampLib\Service;

use Doctrine\ORM\EntityManager;
use EcampCore\Entity\User;
use EcampLib\Acl\Acl;
use EcampLib\Entity\BaseEntity;
use EcampLib\Validation\ValidationException;
use EcampLib\Validation\ValidationForm;
use Zend\InputFilter\Factory;
use Zend\Permissions\Acl\Resource\ResourceInterface;

abstract class ServiceBase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function persist($entity)
    {
        $this->getEntityManager()->persist($entity);

        return $entity;
    }

    protected function remove($entity)
    {
        $this->getEntityManager()->remove($entity);

        return $entity;
    }

    /**
     * @var Factory
     */
    private $inputFilterFactory = null;

    public function setInputFilterFactory(Factory $factory)
    {
        $this->inputFilterFactory = $factory;
    }

    protected function getInputFilterFactory()
    {
        return $this->inputFilterFactory;
    }

    /**
     * @var \EcampLib\Acl\Acl
     */
    private $acl;

    /**
     * @return \EcampLib\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    public function setAcl(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @param ResourceInterface $resource
     * @param $privilege
     */
    protected function aclRequire(ResourceInterface $resource = null, $privilege = null)
    {
        $user = $this->getMe() ?: User::ROLE_GUEST;
        $this->getAcl()->isAllowedException($user, $resource, $privilege);
    }

    /**
     * @param ResourceInterface $resource
     * @param $privilege
     * @return bool
     */
    protected function aclIsAllowed(ResourceInterface $resource = null, $privilege = null)
    {
        $user = $this->getMe() ?: User::ROLE_GUEST;

        return $this->getAcl()->isAllowed($user, $resource, $privilege);
    }

    /**
     * @var \EcampCore\Entity\User
     */
    private $me = null;

    /**
     * @return \EcampCore\Entity\User
     */
    public function getMe()
    {
        return $this->me;
    }

    public function setMe(User $me)
    {
        $this->me = $me;
    }

    /**
     * @var int
     */
    private static $isValidationCounter = 0;
    /**
     * @var ValidationException
     */
    private static $validationException = null;

    protected function beginValidation()
    {
        if (self::$isValidationCounter == 0) {
            self::$validationException = null;
        }
        self::$isValidationCounter++;
    }

    protected function endValidation()
    {
        self::$isValidationCounter--;

        if (self::$validationException != null) {
            throw self::$validationException;
        }
    }

    /**
     * @return ValidationException
     */
    protected function getValidationException()
    {
        return self::$validationException;
    }

    /**
     * @param ValidationException $validationException
     */
    protected function setValidationException(ValidationException $validationException)
    {
        self::$validationException = $validationException;
    }

    /**
     * @return ValidationException
     */
    protected function createValidationException()
    {
        if (self::$validationException == null) {
            self::$validationException = new ValidationException();
        }

        return self::$validationException;
    }

    protected function validateInputArray($data, $inputFilterSpec)
    {
        $inputFilter = $this->getInputFilterFactory()->createInputFilter($inputFilterSpec);
        $inputFilter->setData($data);

        if (! $inputFilter->isValid()) {
            $messages = $inputFilter->getMessages();
            throw new ValidationException($messages);
        }

        return $inputFilter->getValues();
    }

    protected function validationFailed($bool = true, $message = null)
    {
        if ($bool && $message == null) {
            $this->createValidationException();
        }

        if ($bool && $message != null) {
            $this->createValidationException();
        }
    }

    protected function validationAssert($bool = false, $message = null)
    {
        if (!$bool && $message == null) {
            throw new ValidationException();
        }

        if (!$bool && $message != null) {
            throw new ValidationException($message);
        }
    }

    /**
     * @param  BaseEntity                          $entity
     * @return \EcampLib\Validation\ValidationForm
     */
    public function createValidationForm(BaseEntity $entity)
    {
        return new ValidationForm($this->getEntityManager(), $entity);
    }
}
