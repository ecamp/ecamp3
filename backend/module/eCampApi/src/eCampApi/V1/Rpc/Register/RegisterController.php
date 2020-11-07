<?php

namespace eCampApi\V1\Rpc\Register;

use eCamp\Core\Service\RegisterService;
use eCampApi\V1\Rpc\ApiController;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Http\Request;
use Laminas\Json\Json;

class RegisterController extends ApiController {
    /** @var RegisterService */
    private $registerService;

    public function __construct(RegisterService $registerService) {
        $this->registerService = $registerService;
    }

    public function registerAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];

        if (empty($data->username)) {
            return new ApiProblem(400, 'No username provided');
        }
        if (empty($data->email)) {
            return new ApiProblem(400, 'No eMail provided');
        }
        if (empty($data->password)) {
            return new ApiProblem(400, 'No password provided');
        }
        $data->mailAddress = $data->email;
        $user = $this->registerService->register($data);

        if ($user instanceof ApiProblem) {
            return $user;
        }

        return $this->createHalEntity($user, 'e-camp-api.rest.doctrine.user', 'userId');
    }
}
