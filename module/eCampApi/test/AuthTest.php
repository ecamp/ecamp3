<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class AuthTest extends AbstractHttpControllerTestCase
{

    public function testGoogle() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');


        $this->dispatch("/api/auth/hybridauth?provider=google", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
