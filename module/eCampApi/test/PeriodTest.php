<?php

namespace eCamp\ApiTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PeriodTest extends AbstractHttpControllerTestCase
{
    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testPeriodPatch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Content-Type', 'application/json');
        
        $req  = $this->getRequest();
        $req->setContent('{ "start": "2018-02-18", "move_events": false }');
        $this->dispatch("http://localhost:8888/api/period/1234a", 'PATCH');

        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}