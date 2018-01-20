<?php

namespace eCamp\ApiTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AuthTest extends  AbstractHttpControllerTestCase
{

    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testGoogle() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');


        $this->dispatch("http://localhost:8888/api/auth/hybridauth?provider=google", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }



}
