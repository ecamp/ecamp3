<?php

namespace EcampCore\Service\Base;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use EcampCore\Entity\User;
use EcampLib\Entity\BaseEntity;
use EcampLib\Service\ServiceBase as LibServiceBase;
use Zend\Permissions\Acl\Resource\ResourceInterface;

abstract class ServiceBase extends LibServiceBase
{
    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $me = null;

    /** @return EntityManager */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param  BaseEntity      $targetEntity
     * @param  array           $data
     * @param  array           $whitelist
     * @return \Zend\Form\Form
     */
    protected function createValidationForm(BaseEntity $targetEntity, $data, $whitelist = array())
    {
        $entityClass = ClassUtils::getRealClass(get_class($targetEntity));

        $builder = new AnnotationBuilder($this->entityManager);
        $validationForm = $builder->createForm($entityClass);
        $validationForm->setHydrator(new DoctrineObject($this->entityManager));
        $validationForm->bind($targetEntity);
        $validationForm->setValidationGroup($whitelist);
        $validationForm->setData($data);

        return $validationForm;
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
     * @return User
     */
    public function getMe()
    {
        return $this->me;
    }

    public function setMe(User $me = null)
    {
        $this->me = $me;
    }

}
