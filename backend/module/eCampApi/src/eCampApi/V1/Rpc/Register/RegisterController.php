<?php

namespace eCampApi\V1\Rpc\Register;

use eCamp\Core\Service\RegisterService;
use eCampApi\V1\Rpc\ApiController;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Validator\Exception\InvalidArgumentException;
use Laminas\View\Model\ViewModel;

class RegisterController extends ApiController {
    private RegisterService $registerService;

    public function __construct(RegisterService $registerService) {
        $this->registerService = $registerService;
    }

    public function registerAction(): ViewModel {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();

        $data = (null != $content) ? Json::decode($content) : [];

        if (empty($data->username)) {
            throw new InvalidArgumentException('No username provided');
        }
        if (empty($data->email)) {
            throw new InvalidArgumentException('No email provided');
        }
        if (empty($data->password)) {
            throw new InvalidArgumentException('No password provided');
        }
        $data->mailAddress = $data->email;
        $user = $this->registerService->register($data);

        return $this->entityToHalJsonModel($this->createHalEntity($user, 'e-camp-api.rest.doctrine.user', 'userId'));
    }
}
