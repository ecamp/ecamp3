<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * Finds out which resource the operation of a request delegates its security to, see HttpCacheConfig.
 * It evaluates to the resource whose read access is required to read the response.
 */
final class CacheSecurityDelegateResolver {
    private readonly ExpressionLanguage $expressionLanguage;

    public function __construct(
        private readonly HttpCacheConfigResolver $configResolver,
        private readonly EntityManagerInterface $em,
        private readonly IriConverterInterface $iriConverter,
    ) {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @return string[] names of the variables that can be used in the expression of the operation
     */
    public static function getVariableNames(Operation $operation): array {
        if (!$operation instanceof HttpOperation || !$operation instanceof CollectionOperationInterface) {
            return ['object'];
        }

        $names = [];
        foreach ($operation->getUriVariables() ?? [] as $link) {
            if ($link instanceof Link && null !== $link->getFromClass()) {
                $names[] = self::getVariableName($link);
            }
        }

        return $names;
    }

    /**
     * @return null|string the IRI of the resource that the response delegates its security to
     */
    public function resolveDelegate(Request $request, HttpCacheConfig $config): ?string {
        $operation = $this->configResolver->getOperation($request);
        if (null === $config->securityDelegate || null === $operation) {
            return null;
        }

        $delegate = $this->expressionLanguage->evaluate($config->securityDelegate, $this->getVariables($request, $operation));
        if (null === $delegate) {
            return null;
        }

        if (!is_object($delegate)) {
            throw new \LogicException("The security delegate of {$operation->getName()} must evaluate to a resource.");
        }

        return $this->iriConverter->getIriFromResource($delegate);
    }

    /**
     * @return array<string,mixed>
     */
    private function getVariables(Request $request, Operation $operation): array {
        if (!$operation instanceof HttpOperation || !$operation instanceof CollectionOperationInterface) {
            return ['object' => $request->attributes->get('data')];
        }

        $variables = [];
        foreach ($operation->getUriVariables() ?? [] as $parameterName => $link) {
            if ($link instanceof Link && null !== $link->getFromClass()) {
                $variables[self::getVariableName($link)] = $this->em->find($link->getFromClass(), $request->attributes->get($parameterName));
            }
        }

        return $variables;
    }

    private static function getVariableName(Link $link): string {
        return $link->getSecurityObjectName() ?? $link->getToProperty() ?? $link->getFromProperty();
    }
}
