<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class CampTypeApiTest extends  AbstractHttpControllerTestCase
{

    public function testCreateCamp() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Authorization', 'Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==');

        /** @var Request $req */
        $req  = $this->getRequest();


        $this->dispatch("/api/camp_type", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
