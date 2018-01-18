<?php

namespace eCamp\Api\Controller;

use Zend\Authentication\Adapter\Http;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController
{
    public function indexAction() {

        $httpAdapter = new Http([
            'accept_schemes' => 'basic',
            'realm' => 'eCamp',
            'nonce_timeout' => 3600
        ]);

        $response = $this->getResponse();

        $httpAdapter->setBasicResolver(new Resolver());
        $httpAdapter->setRequest($this->getRequest());
        $httpAdapter->setResponse($response);

        $authService = new AuthenticationService();
        $result = $authService->authenticate($httpAdapter);

        return $response;
    }

    public function infoAction() {
        $authService = new AuthenticationService();
        var_dump($authService->getIdentity());
        die();
    }
}

class Resolver implements Http\ResolverInterface
{
    public function resolve($username, $realm, $password = null) {
        // var_dump($username, $realm, $password);

        return [$username, $realm];
    }
}
