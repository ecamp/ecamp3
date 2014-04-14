<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractJobBase;

class Zf2CliJob extends AbstractJobBase
{
    public function __construct($command = null)
    {
        parent::__construct('cli');

        $this->command = $command;
    }

    public function executeJob()
    {
        $argv = explode(' ', $this->command);
        array_unshift($argv, 'Zf2CliJob.php');

        $argc = count($argv) + 1;
        $_SERVER['argv'] = $argv;
        $_SERVER['argc'] = $argc;

        $app = \Zend\MVc\Application::init(require 'config/application.config.php');
        $responseSender = $app->getServiceManager()->get('SendResponseListener');

        $responseSender->getEventManager()->attach(
            \Zend\Mvc\ResponseSender\SendResponseEvent::EVENT_SEND_RESPONSE,
            new \EcampLib\Mvc\ResponseSender\ConsoleResponseSender(false)
        );

        $app->run();
    }

}
