<?php

namespace eCamp\Lib\Controller;

use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController {
    /**
     * @param null|int $statusCode
     */
    protected function emptyResponse($statusCode = null): Response {
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode ?? Response::STATUS_CODE_204);

        return $response;
    }
}
