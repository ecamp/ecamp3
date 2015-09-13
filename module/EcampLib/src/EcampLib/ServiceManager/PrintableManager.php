<?php

namespace EcampLib\ServiceManager;

use EcampLib\Printable\PrintableInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class PrintableManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed                      $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof PrintableInterface) {
            return;
        }

        throw new Exception\RuntimeException('Wrong instance of plugin');
    }

}
