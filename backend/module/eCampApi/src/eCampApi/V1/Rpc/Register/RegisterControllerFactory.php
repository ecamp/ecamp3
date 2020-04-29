<?php

namespace eCampApi\V1\Rpc\Register;

use eCamp\Core\Service\RegisterService;

class RegisterControllerFactory {
    public function __invoke($controllers) {
        /** @var RegisterService $registerService */
        $registerService = $controllers->get(RegisterService::class);

        return new RegisterController($registerService);
    }
}
