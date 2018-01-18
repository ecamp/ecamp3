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

    public function testOrganizationFetch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch("http://localhost:8888/api/organization/1");
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }
}
