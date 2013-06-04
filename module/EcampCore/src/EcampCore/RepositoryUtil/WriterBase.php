<?php

namespace EcampCore\RepositoryUtil;

use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

abstract class WriterBase
{
    /**
     * @var Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        EntityManager $em
    ){
        $this->serviceLocator = $serviceLocator;
        $this->em = $em;
    }

    protected function getRepositories($moduleNamespace)
    {
        $repositories = array();

        $classMetadataList = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($classMetadataList as $classMetadata) {

            if( $classMetadata->name != 'EcampCore\Entity\BaseEntity' &&
                substr($classMetadata->name, 0, strlen($moduleNamespace)) == $moduleNamespace
            ){
                $repositories[] = $classMetadata->name;
            }
        }

        return $repositories;
    }

    protected function getClassname($fqcn)
    {
        $pos = strrpos($fqcn, '\\');

        if ($pos) {
            return substr($fqcn, 1 + $pos);
        } else {
            return $fqcn;
        }
    }

    protected function getRepositoryAlias($fqcn)
    {
        return strtolower(str_replace('\\Entity\\', '.repo.', $fqcn));
    }

    protected function getRepositoryTrait($traitNamespace, $fqcn)
    {
        return
            ($traitNamespace ? $traitNamespace . '\\' : "") .
            $this->getClassname($fqcn) . 'Trait';
    }

    protected function getRepositoryTraitClass($fqcn)
    {
        return $this->getClassname($fqcn) . 'RepositoryTrait';
    }

    protected function getRepositoryClass($fqcn)
    {
        $classMetadata = $this->em->getMetadataFactory()->getMetadataFor($fqcn);

        return $classMetadata->customRepositoryClassName ?: 'Doctrine\ORM\EntityRepository';
    }

    protected function getRepositoryProperty($fqcn)
    {
        return lcfirst($this->getClassname($fqcn)) . 'Repository';
    }

    protected function getGetterMethod($fqcn)
    {
        return 'get' . ucfirst($this->getClassname($fqcn)) . 'Repository';
    }

    protected function getSetterMethod($fqcn)
    {
        return 'set' . ucfirst($this->getClassname($fqcn)) . 'Repository';
    }

}
