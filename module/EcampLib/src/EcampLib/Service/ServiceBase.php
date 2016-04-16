<?php

namespace EcampLib\Service;

use EcampLib\Acl\Acl;
use EcampLib\Validation\ValidationException;
use Zend\Config\Config;

abstract class ServiceBase
{

    /**
     * @var array
     */
    private $configArray;

    /**
     * @return array
     */
    public function getConfigArray()
    {
        return $this->configArray;
    }

    public function getConfig($name = null)
    {
        $config = new Config($this->configArray);
        if ($name != null) {
            return $config->get($name);
        }

        return $config;
    }

    public function setConfigArray(array $configArray)
    {
        $this->configArray = $configArray;
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
