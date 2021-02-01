<?php

namespace eCamp\Core\Controller\Auth;

class PbsMiDataControllerFactory extends HitobitoControllerFactory {
    protected function getControllerClass(): string {
        return PbsMiDataController::class;
    }
}
