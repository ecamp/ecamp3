<?php

namespace EcampLib\Service;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

use EcampCore\Entity\User;
use EcampLib\Acl\Acl;
use EcampLib\Entity\BaseEntity;
use EcampLib\Validation\ValidationException;
use Zend\EventManager\EventManagerInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;

abstract class ServiceBase
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @return EntityManager
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
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }


    /**
     * @param  BaseEntity      $targetEntity
     * @param  array           $data
     * @param  array           $whitelist
     * @return \Zend\Form\Form
     */
    protected function createValidationForm(BaseEntity $targetEntity, $data, $whitelist = array())
    {
        $entityClass = ClassUtils::getRealClass(get_class($targetEntity));

        $builder = new AnnotationBuilder($this->em);
        $validationForm = $builder->createForm($entityClass);
        $validationForm->setHydrator(new DoctrineObject($this->em));
        $validationForm->bind($targetEntity);
        $validationForm->setValidationGroup($whitelist);
        $validationForm->setData($data);

        return $validationForm;
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

    protected function validationAssert($bool = false, $message = null)
    {
        if (!$bool && $message == null) {
            throw new ValidationException();
        }

        if (!$bool && $message != null) {
            throw new ValidationException($message);
        }
    }

}
