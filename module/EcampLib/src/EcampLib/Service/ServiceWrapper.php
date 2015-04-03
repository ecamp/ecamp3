<?php

namespace EcampLib\Service;

class ServiceWrapper
{

    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->service->getEntityManager();
    }

    public function __call($method, $args)
    {
        try {
            $result = call_user_func_array(array($this->service, $method), $args);
            $this->getEntityManager()->flush();

            return $result;
        } catch (\Exception $ex) {
            $this->getEntityManager()->getConnection()->rollBack();
            throw $ex;
        }
    }

}
