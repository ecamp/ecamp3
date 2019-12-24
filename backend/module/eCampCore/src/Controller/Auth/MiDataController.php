<?php

namespace eCamp\Core\Controller\Auth;

class MiDataController extends HitobitoController {

    protected function getProviderName() {
        return 'MiData';
    }

    protected function getCallbackRoute() {
        return 'ecamp.auth/midata';
    }
}
