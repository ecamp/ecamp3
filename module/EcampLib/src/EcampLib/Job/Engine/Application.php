<?php

namespace EcampLib\Job\Engine;

use Zend\Mvc\ApplicationInterface;

class Application
{
    /**
     * @var ApplicationInterface
     */
    private static $application;


    /**
     * @return ApplicationInterface
     */
    public static function Instance()
    {
        return self::$application;
    }

    public static function SetInstance(ApplicationInterface $application)
    {
        self::$application = $application;
    }
}
