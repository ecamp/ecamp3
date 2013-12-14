<?php

namespace EcampLib\Service;

class ServiceWrapper
{

    private $service;

    public function __construct(ServiceBase $service)
    {
        $this->service = $service;
    }

    public function __call($method, $args)
    {
        try {
            $this->service->getEntityManager()->getConnection()->beginTransaction();
            $result = call_user_func_array(array($this->service, $method), $args);

            $this->service->getEntityManager()->getConnection()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->service->getEntityManager()->getConnection()->rollBack();
            throw $ex;
        }
    }

}
