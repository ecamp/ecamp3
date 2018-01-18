<?php

namespace eCamp\ApiTest;

use Zend\Http\Request;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CampApiTest extends  AbstractHttpControllerTestCase
{

    public function setUp() {
        $data = include __DIR__ . '/../../../config/application.config.php';
        $this->setApplicationConfig($data);

        parent::setUp();
    }

    public function testCreateCamp() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        /** @var Request $req */
        $req  = $this->getRequest();
        /** @var \Zend\Stdlib\ParametersInterface $post */
        $post = $req->getPost();
        $post->set('name', 'test-camp-5');
        $post->set('title', 'test-camp-5');
        $post->set('motto', 'test-camp-5');
        $post->set('camp_type_id', '6faac6e1');
        $post->set('owner_id', '528beb88');
        $post->set('creator_id', '528beb88');
        $post->set('start', new \DateTime('02.02.2017'));
        $post->set('end', new \DateTime('11.02.2017'));


        $this->dispatch("http://localhost:8888/api/camp", 'POST');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
