<?php

namespace EcampLib\Job;

use Zend\Mvc\Application as ZendApplication;

class Application extends ZendApplication
{
    /** @var ZendApplication */
    private static $application = null;

    public static function Set(ZendApplication $application)
    {
        self::$application = $application;
    }

    /**
     * @return ZendApplication
     */
    public static function Get()
    {
        return self::$application;
    }
}
