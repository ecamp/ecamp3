<?php

namespace EcampLib\Service;

use Zend\EventManager\Event;

class ServiceEvent extends Event
{
    const SERVICE_CREATED       = 'service.created';
    const SERVICE_CALL_PRE      = 'service.pre';
    const SERVICE_CALL_POST     = 'service.post';
    const SERVICE_CALL_SUCCESS  = 'service.success';
    const SERVICE_CALL_ERROR    = 'service.error';
}