<?php

namespace eCamp\ApiTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class UserApiTest extends  AbstractHttpControllerTestCase
{

    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testUserFetch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch("http://localhost:8888/api/user");
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

    public function testUserPatch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $req  = $this->getRequest();
        $req->setContent('{ "username": "test-user-22" }');
        $this->dispatch("http://localhost:8888/api/user/1", 'PATCH');

        $resp = $this->getResponse();
    }
}
