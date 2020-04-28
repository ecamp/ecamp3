<?php

//
//namespace eCamp\Lib\Service;
//
//use Interop\Container\ContainerInterface;
//use Psr\Container\ContainerExceptionInterface;
//use Psr\Container\NotFoundExceptionInterface;
//use Zend\Hydrator\HydratorPluginManager;
//use Zend\ServiceManager\Factory\FactoryInterface;
//
//abstract class BaseServiceFactory implements FactoryInterface
//{
//    /**
//     * @param ContainerInterface $container
//     * @param string $name
//     * @return mixed
//     * @throws ContainerExceptionInterface
//     * @throws NotFoundExceptionInterface
//     */
//    protected function getEntityManager(ContainerInterface $container, $name = 'orm_default')
//    {
//        return $container->get('doctrine.entitymanager.' . $name);
//    }
//
//    /**
//     * @param ContainerInterface $container
//     * @param $className
//     * @return mixed
//     * @throws ContainerExceptionInterface
//     * @throws NotFoundExceptionInterface
//     */
//    protected function getHydrator(ContainerInterface $container, $className)
//    {
//        $hydrators = $container->get(HydratorPluginManager::class);
//        return $hydrators->get($className);
//    }
//}
