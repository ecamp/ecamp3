<?php

namespace eCamp\WebTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class IndexTest extends AbstractHttpControllerTestCase
{

    public function testIndex() {
        $this->dispatch("/", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }

    public function testUserRouter() {

        $this->dispatch("/user/Pirmin%20Mattmann", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }

    public function testCampRouter() {

        $this->dispatch("/user/Pirmin%20Mattmann/camp/test", 'GET');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }

}