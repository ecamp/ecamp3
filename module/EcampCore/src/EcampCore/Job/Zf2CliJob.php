<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractJobBase;
use EcampLib\Mvc\ResponseSender\ConsoleResponseSender;
use Zend\Mvc\ApplicationInterface;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Mvc\SendResponseListener;

class Zf2CliJob extends AbstractJobBase
{
    public function __construct($command = null)
    {
        $this->command = $command;
    }

    public function doExecute(ApplicationInterface $app)
    {
        $argv = explode(' ', $this->command);
        array_unshift($argv, 'Zf2CliJob.php');

        $argc = count($argv) + 1;
        $_SERVER['argv'] = $argv;
        $_SERVER['argc'] = $argc;

        /** @var SendResponseListener $responseSender */
        $responseSender = $app->getServiceManager()->get('SendResponseListener');

        $event = $responseSender->getEventManager()->attach(
            SendResponseEvent::EVENT_SEND_RESPONSE,
            new ConsoleResponseSender(false)
        );

        $app->run();

        $responseSender->getEventManager()->detach($event);
    }

}
