<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ContentNode;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @template-implements ProviderInterface<ContentNode>
 */
class ContentNodeCollectionProvider implements ProviderInterface {
    public function __construct(
        private readonly ProviderInterface $decorated,
        private readonly RequestStack $requestStack
    ) {}

    #[\Override]
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null {
        $request = $this->requestStack->getCurrentRequest();
        $hasFilter = $request?->query->has('camp') || $request?->query->has('period') || $request?->query->has('root');

        if (!$hasFilter) {
            throw new BadRequestHttpException('Filter on camp, period or root is required');
        }

        return $this->decorated->provide($operation, $uriVariables, $context);
    }
}
