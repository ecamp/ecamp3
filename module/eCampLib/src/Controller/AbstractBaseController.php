<?php

namespace eCamp\Lib\Controller;

use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController
{

    /**
     * @param null $statusCode
     * @return Response
     */
    protected function emptyResponse($statusCode = null) {
        if ($statusCode == null) {
            $statusCode = Response::STATUS_CODE_204;
        }

        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        return $response;
    }

}