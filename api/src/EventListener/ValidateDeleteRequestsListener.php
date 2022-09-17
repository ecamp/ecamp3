<?php

namespace App\EventListener;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidateDeleteRequestsListener implements EventSubscriberInterface {
    public function __construct(
        private ValidatorInterface $validator,
        private ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
    }

    public static function getSubscribedEvents(): array {
        return [
            // Run this event with the same priority as the ValidateListener
            // ValidateListener currently does not validate DELETE Request
            KernelEvents::VIEW => ['validateDeleteRequest', 64],
        ];
    }

    /**
     * @throws \ApiPlatform\Core\Exception\ResourceClassNotFoundException
     */
    public function validateDeleteRequest(ViewEvent $event): void {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if (!$request->isMethod('DELETE')
            || !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !$attributes['receive']
        ) {
            return;
        }

        $resourceMetadata = $this->resourceMetadataFactory->create($attributes['resource_class']);

        $validationGroups = $resourceMetadata->getOperationAttribute($attributes, 'validation_groups', ['delete'], true);
        $this->validator->validate($controllerResult, ['groups' => $validationGroups]);
    }
}
