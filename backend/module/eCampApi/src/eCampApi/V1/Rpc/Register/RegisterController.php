<?php

namespace eCampApi\V1\Rpc\Register;

use eCamp\Core\Service\RegisterService;
use eCampApi\V1\Rpc\ApiController;
use Zend\Http\Request;
use Zend\Json\Json;
use ZF\ApiProblem\ApiProblem;

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

        if (!isset($data->username)) {
            return new ApiProblem(400, 'No username provided');
        }
        if (!isset($data->email)) {
            return new ApiProblem(400, 'No eMail provided');
        }
        if (!isset($data->password)) {
            return new ApiProblem(400, 'No password provided');
        }

        $user = $this->registerService->register($data->username, $data->email, $data->password);

        if ($user instanceof ApiProblem) {
            return $user;
        }

        return $this->createHalEntity($user, 'e-camp-api.rest.doctrine.user', 'user_id');
    }
}
