<?php

namespace eCamp\Lib\ServiceManager;

interface EntityFilterManagerAware
{
    /**
     * @param EntityFilterManager $entityFilterManager
     */
    public function setEntityFilterManager(EntityFilterManager $entityFilterManager);

}
