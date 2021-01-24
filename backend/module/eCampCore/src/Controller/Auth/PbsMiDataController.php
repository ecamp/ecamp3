<?php

namespace eCamp\Core\Controller\Auth;

class PbsMiDataController extends HitobitoController {
    protected function getProviderName(): string {
        return 'PbsMiData';
    }

    protected function getCallbackRoute(): string {
        return 'ecamp.auth/pbsmidata';
    }
}
