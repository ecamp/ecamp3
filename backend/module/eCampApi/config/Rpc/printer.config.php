<?php

use eCampApi\V1\Rpc\Printer\PrinterController;
use eCampApi\V1\RpcConfig;

return RpcConfig::forRoute('e-camp-api.rpc.printer')
    ->setController(PrinterController::class)
    ->setRoute('/api/printer')
    ->addParameterDefault('action', 'index')
    ->setAllowedHttpMethods('POST')
    ->build()
;
