<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ChecklistItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @template-implements ProviderInterface<ChecklistItem>
 */
class ChecklistItemCollectionProvider implements ProviderInterface {
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly RequestStack $requestStack
    ) {}

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null {
        $request = $this->requestStack->getCurrentRequest();
        $hasFilter = $request?->query->has('checklist') || $request?->query->has('checklist_camp');

        if (!$hasFilter) {
            throw new BadRequestHttpException('Filter on checklist or checklist.camp is required');
        }

        return $this->decorated->provide($operation, $uriVariables, $context);
    }
}
