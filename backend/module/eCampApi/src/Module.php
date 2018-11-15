<?php

namespace eCamp\Api;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Auth\Identity\AuthenticatedIdentity;
use eCamp\Core\Entity\User;
use OAuth2\Encryption\Jwt;
use Zend\Config\Factory;
use Zend\Http\Request;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use ZF\MvcAuth\Identity\GuestIdentity;
use ZF\MvcAuth\MvcAuthEvent;

class Module {

    /** @var ServiceManager $serviceManager */
    private $serviceManager;

    public function getConfig() {
        return Factory::fromFiles(array_merge(
            [__DIR__ . '/../config/module.config.php'],
            glob(__DIR__ . '/../config/autoload/*.*'),
            glob(__DIR__ . '/../config/autoload/V1/*.*')
        ));
    }

    public function onBootstrap(MvcEvent $e) {
        $this->serviceManager = $e->getApplication()->getServiceManager();
        /** @var Application $app */
        $app = $e->getTarget();
        $events = $app->getEventManager();
        $events->attach('authentication', [$this, 'onAuthentication'], 100);
        $events->attach('authorization', [$this, 'onAuthorization'], 100);
    }

    /**
     * @param MvcAuthEvent $e
     * @return AuthenticatedIdentity|GuestIdentity
     */
    public function onAuthentication(MvcAuthEvent $e) {
        $guest = new GuestIdentity();
        /** @var Request $request */
        $request = $e->getMvcEvent()->getRequest();
        $header = $request->getHeader('Authorization');

        if (!$header) {
            return $guest;
        }

        $token = preg_replace('/^Bearer /', '', $header->getFieldValue());
        $jwt = new Jwt();
        $key = $this->serviceManager->get('config')['crypto_key'];
        $tokenData = $jwt->decode($token, $key);

        // If the token is invalid, give up
        if (!$tokenData) {
            return $guest;
        }

        $entityManager = $this->serviceManager->get(EntityManager::class);
        $user = $entityManager
            ->getRepository(User::class)
            ->findByUsername($tokenData['username']);

        return new AuthenticatedIdentity($user);
    }

    public function onAuthorization(MvcAuthEvent $e) {
        /* @var $authorization \ZF\MvcAuth\Authorization\AclAuthorization */
        $authorization = $e->getAuthorizationService();
        $identity = $e->getIdentity();
        $resource = $e->getResource();

        // now set up additional ACLs...
    }
}
