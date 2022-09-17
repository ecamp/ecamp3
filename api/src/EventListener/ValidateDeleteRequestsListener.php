<?php

namespace App\EventListener;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Util\RequestAttributesExtractor;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidateDeleteRequestsListener implements EventSubscriberInterface {
    public function __construct(
        private ValidatorInterface $validator,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory
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
     * @throws \ApiPlatform\Exception\ResourceClassNotFoundException
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

        $resourceMetadata = $this->resourceMetadataCollectionFactory->create($attributes['resource_class']);

        $validationGroups = $resourceMetadata->getOperation('delete')->getValidationContext()['groups'] ?? ['delete'];
        $this->validator->validate($controllerResult, ['groups' => $validationGroups]);
    }
}
