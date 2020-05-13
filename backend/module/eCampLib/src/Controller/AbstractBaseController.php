<?php

namespace eCamp\Lib\Controller;

use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;

abstract class AbstractBaseController extends AbstractActionController {
    /**
     * @param null $statusCode
     *
     * @return Response
     */
    protected function emptyResponse($statusCode = null) {
        if (null == $statusCode) {
            $statusCode = Response::STATUS_CODE_204;
        }

        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode($statusCode);

        return $response;
    }
}
