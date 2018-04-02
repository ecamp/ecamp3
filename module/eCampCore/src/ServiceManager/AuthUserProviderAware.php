<?php

namespace eCamp\Core\ServiceManager;

use eCamp\Core\Auth\AuthUserProvider;

interface AuthUserProviderAware
{
    /**
     * @param AuthUserProvider $authUserProvider
     */
    public function setAuthUserProvider(AuthUserProvider $authUserProvider);

}
