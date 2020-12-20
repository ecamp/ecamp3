<?php

namespace eCampApi\util;

use Laminas\Http\Response;

class HttpResponseBuilder {
    private int $httpStatusCode;

    public function __construct(int $httpStatusCode) {
        $this->httpStatusCode = $httpStatusCode;
    }

    public static function ok(): HttpResponseBuilder {
        return new HttpResponseBuilder(Response::STATUS_CODE_200);
    }

    public function build(): Response {
        $response = new Response();
        $response->setStatusCode($this->httpStatusCode);

        return $response;
    }
}
