<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\JobService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class JobServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return JobService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $jobRespRepository = $services->get('EcampCore\Repository\JobResp');

        return new JobService($jobRespRepository);
    }
}
