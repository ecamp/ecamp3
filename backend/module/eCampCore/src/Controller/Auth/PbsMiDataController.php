<?php

namespace eCamp\Core\Controller\Auth;

class PbsMiDataController extends HitobitoController {

    protected function getProviderName() {
        return 'PbsMiData';
    }

    protected function getCallbackRoute() {
        return 'ecamp.auth/pbsmidata';
    }
}
