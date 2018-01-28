<?php

namespace eCamp\ApiTest;

use Zend\Http\Request;
use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class CampCollaborationApiTest extends  AbstractHttpControllerTestCase
{

    public function testCreateCampCollaboration() {

        $headers = $this->getRequest()->getHeaders();
        $headers->addheaderLine('Content-Type', 'application/json');
        $headers->addHeaderLine('Accept', 'application/json');

        /** @var Request $req */
        $req  = $this->getRequest();

        $req->setContent('{
            "camp_id":  "235c3782",
            "user_id":  "528beb88",
            "role":     "member"
        }');


        /** @var \Zend\Stdlib\ParametersInterface $post */
        /*
        $post = $req->getPost();
        $post->set('camp_id', '235c3782');
        $post->set('user_id', '528beb88');
        $post->set('role', 'member');
        */

        $this->dispatch("/api/camp_collaboration", 'POST');
        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();

    }

}
