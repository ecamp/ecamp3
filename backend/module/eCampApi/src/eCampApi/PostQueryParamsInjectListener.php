<?php

namespace eCampApi;

use Laminas\ApiTools\ContentValidation\ContentValidationListener;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;

/**
 * This listener merges query params into body params for POST requests
 * Happens before validation to ensure input filters are applied to query params, too.
 */
class PostQueryParamsInjectListener implements ListenerAggregateInterface {
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1): void {
        $this->listeners[] = $events->attach(ContentValidationListener::EVENT_BEFORE_VALIDATE, [$this, 'onBeforeValidate']);
    }

    public function onBeforeValidate(MvcEvent $e): void {
        /** @var HttpRequest $request */
        $request = $e->getRequest();

        if ('POST' === $request->getMethod()) {
            $dataContainer = $e->getParam('LaminasContentNegotiationParameterData', false);

            $queryParams = $dataContainer->getQueryParams();
            $bodyParams = $e->getParam('Laminas\ApiTools\ContentValidation\ParameterData') ?: [];

            // merge queryParams with POST body (later wins)
            $data = array_merge($queryParams, $bodyParams);

            $e->setParam('Laminas\ApiTools\ContentValidation\ParameterData', $data);
        }
    }
}
