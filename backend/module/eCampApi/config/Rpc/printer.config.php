<?php

use eCampApi\RpcConfigFactory;
use eCampApi\V1\Rpc\Printer\PrinterController;

return RpcConfigFactory::forRoute('e-camp-api.rpc.printer')
    ->setController(PrinterController::class)
    ->setRoute('/api/printer')
    ->setAllowedHttpMethods('POST')
    ->addParameterDefault('action', 'index')
    ->build()
;
