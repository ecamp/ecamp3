<?php

namespace EcampLib\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions
{
    protected $repositoryMappings = array();
    protected $entityFormMappings = array();
    protected $entityFormElementMappings = array();
    protected $serviceMappings = array();

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function setRepositoryMappings(array $repositoryMappings)
    {
        $this->repositoryMappings = $repositoryMappings;
    }

    public function getRepositoryMappings()
    {
        return $this->repositoryMappings;
    }

    public function setEntityFormMappings(array $entityFormMappings)
    {
        $this->entityFormMappings = $entityFormMappings;
    }

    public function getEntityFormMappings()
    {
        return $this->entityFormMappings;
    }

    public function setEntityFormElementMappings(array $entityFormElementMappings)
    {
        $this->entityFormElementMappings = $entityFormElementMappings;
    }

    public function getEntityFormElementMappings()
    {
        return $this->entityFormElementMappings;
    }

    public function setServiceMappings(array $serviceMappings)
    {
        $this->serviceMappings = $serviceMappings;
    }

    public function getServiceMappings()
    {
        return $this->serviceMappings;
    }

}
