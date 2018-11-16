<?php

namespace eCamp\Lib\Auth\Storage;

use eCamp\Lib\Util\UrlUtils;
use Zend\Authentication\Storage\StorageInterface;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;

class AuthHeaderAndRedirectQueryParam implements StorageInterface {
    const HEADER_NAME = 'Authorization';
    const QUERY_PARAM_NAME = 'token';

    /** @var Request $request */
    private $request;
    /** @var EventManager $eventManager */
    private $eventManager;

    private $contents;
    private $hasRead = false;

    /**
     * RedirectQueryParam constructor.
     * @param Request $request
     * @param EventManager $eventManager
     */
    public function __construct(Request $request, EventManager $eventManager) {
        $this->request = $request;
        $this->eventManager = $eventManager;
    }

    public function isEmpty() {
        return empty($this->read());
    }

    /**
     * The token will be read from the Authorization header of the request.
     * @return mixed
     */
    public function read() {
        if (!$this->hasRead) {
            if ($this->hasHeader()) {
                $this->contents = $this->readHeader();
            }
            $this->hasRead = true;
        }

        return $this->contents;
    }

    /**
     * Writing will add a query param to the redirect if possible. This will only be triggered when the token
     * to be written differs from the one that is currently read, i.e. when a new token was generated.
     * @param mixed $contents
     */
    public function write($contents) {
        if ($this->contents != $contents) {
            $this->eventManager->attach(
                MvcEvent::EVENT_FINISH,
                function (MvcEvent $e) {
                    $response = $e->getResponse();
                    if ($response instanceof Response) {
                        $this->close($response);
                    }
                }
            );
        }

        $this->contents = $contents;
        $this->hasRead = true;
    }

    public function clear() {
        $this->contents = null;
        $this->hasRead = true;
    }

    public function close(Response $response) {
        if (!$response->isRedirect()) {
            return;
        }

        if (!$this->hasRead) {
            $this->read();
        }

        if ($this->contents !== null) {
            $origLocationHeader = $response->getHeaders()->get('Location');
            $origRedirect = $origLocationHeader->getFieldValue();
            $newRedirect = UrlUtils::addQueryParameterToUrl($origRedirect, self::QUERY_PARAM_NAME, $this->contents);

            $response->getHeaders()->removeHeader($origLocationHeader);
            $response->getHeaders()->addHeaderLine('Location', $newRedirect);
        }
    }

    private function hasHeader() {
        if (!($this->request instanceof Request)) {
            return false;
        }

        return $this->request->getHeaders()->has(self::HEADER_NAME);
    }

    private function getHeader() {
        if (!($this->request instanceof Request)) {
            return null;
        }

        return $this->request->getHeader(self::HEADER_NAME);
    }

    private function readHeader() {
        return str_replace('Bearer ', '', $this->getHeader()->getFieldValue());
    }
}
