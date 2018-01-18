<?php

namespace eCamp\ApiTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CampTypeApiTest extends  AbstractHttpControllerTestCase
{

    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testCreateCamp() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Authorization', 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==');

        /** @var Request $req */
        $req  = $this->getRequest();


        $this->dispatch("http://localhost:8888/api/camp_type", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
