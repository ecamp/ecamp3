<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;
use Zend\Http\Request;

abstract class AbstractApiTestCase extends AbstractHttpControllerTestCase
{
    /**
     * @param $url
     * @param array $params
     * @throws \Exception
     */
    public function dispatchGet($url, $params = []) {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch($url, Request::METHOD_GET, $params);
    }

    /**
     * @param $url
     * @param array $params
     * @throws \Exception
     */
    public function dispatchPost($url, $params = []) {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch($url, Request::METHOD_POST, $params);
    }

    /**
     * @param $url
     * @param array $params
     * @throws \Exception
     */
    public function dispatchPatch($url, $params = []) {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch($url, Request::METHOD_PATCH, $params);
    }

    /**
     * @param $url
     * @param array $params
     * @throws \Exception
     */
    public function dispatchDelete($url, $params = []) {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');

        $this->dispatch($url, Request::METHOD_DELETE, $params);
    }
}