<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\MaterialItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @template-implements ProviderInterface<MaterialItem>
 */
class MaterialItemCollectionProvider implements ProviderInterface {
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly RequestStack $requestStack
    ) {}

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null {
        $request = $this->requestStack->getCurrentRequest();
        $hasFilter = $request?->query->has('camp') || $request?->query->has('period') || $request?->query->has('materialList') || $request?->query->has('materialNode');

        if (!$hasFilter) {
            throw new BadRequestHttpException('Filter on camp, period, materialList or materialNode is required');
        }

        return $this->decorated->provide($operation, $uriVariables, $context);
    }
}
