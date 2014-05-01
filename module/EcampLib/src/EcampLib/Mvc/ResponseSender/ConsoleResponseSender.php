<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EcampLib\Mvc\ResponseSender;

use Zend\Console\Response;
use Zend\Mvc\ResponseSender\SendResponseEvent;
use Zend\Mvc\ResponseSender\ResponseSenderInterface;

class ConsoleResponseSender implements ResponseSenderInterface
{

    private $doExit;

    public function __construct($doExit = true)
    {
        $this->doExit = $doExit;
    }

    /**
     * Send content
     *
     * @param  SendResponseEvent     $event
     * @return ConsoleResponseSender
     */
    public function sendContent(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        echo $response->getContent();
        $event->setContentSent();

        return $this;
    }

    /**
     * Send the response
     *
     * @param SendResponseEvent $event
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $this->sendContent($event);
        $event->stopPropagation(true);

        if ($this->doExit) {
            $errorLevel = (int) $response->getMetadata('errorLevel',0);
            exit($errorLevel);
        }
    }
}
