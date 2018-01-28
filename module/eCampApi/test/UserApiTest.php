<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class UserApiTest extends AbstractHttpControllerTestCase
{

    public function testUserFetch() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch("/api/user");
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
        $this->dispatch("/api/user/1", 'PATCH');

        $resp = $this->getResponse();

        $this->assertNotRedirect();
    }
}
