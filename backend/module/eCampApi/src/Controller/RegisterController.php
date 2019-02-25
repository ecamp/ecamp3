<?php

namespace eCamp\Api\Controller;

use eCamp\Core\Service\RegisterService;
use Zend\Http\Request;
use Zend\Json\Json;
use ZF\ApiProblem\ApiProblem;

class RegisterController extends ApiController
{
    /** @var RegisterService */
    private $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function registerAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = ($content != null) ? Json::decode($content) : [];

        if(!isset($data->username)) {
            return new ApiProblem(400, "No username provided");
        }
        if(!isset($data->email)) {
            return new ApiProblem(400, "No eMail provided");
        }
        if(!isset($data->pw)) {
            return new ApiProblem(400, "No password provided");
        }

        $user = $this->registerService->register($data->username, $data->email, $data->pw);

        if ($user instanceof ApiProblem) {
            return $user;
        }

        $plugin = $this->plugin('Hal');
        $entity = $plugin->createEntity($user, 'ecamp.api.user', 'user_id');

        return $entity;
    }
}