<?php

declare(strict_types=1);

namespace App\HttpCache;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\Util\OperationRequestInitiatorTrait;
use ApiPlatform\State\Util\RequestAttributesExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\Request;

/**
 * Finds out which resource the operation of a request delegates its security to, which is declared as an expression
 * in the extra property "cache_security_delegate" of the operation. Like the "security" expression of an operation,
 * it can use the variable "object" for the resource of an item operation, or the linked resources of a subresource
 * collection operation by the name used in their link, e.g. "camp".
 * It evaluates to the resource whose read access is required to read the response, or to "public" if none is.
 */
final class CacheSecurityDelegateResolver {
    use OperationRequestInitiatorTrait;

    private const string ENTRYPOINT_ROUTE = 'api_entrypoint';

    private readonly ExpressionLanguage $expressionLanguage;

    public function __construct(
        ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly EntityManagerInterface $em,
        private readonly IriConverterInterface $iriConverter,
    ) {
        $this->resourceMetadataCollectionFactory = $resourceMetadataCollectionFactory;
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
     * @return null|string the declared expression, or null if the operation delegates nothing
     */
    public function getDeclaredDelegate(Request $request): ?string {
        if (self::ENTRYPOINT_ROUTE === $request->attributes->get('_route')) {
            return CacheSecurityDelegateListener::DELEGATE_PUBLIC;
        }

        $declared = $this->getOperation($request)?->getExtraProperties()[CacheSecurityDelegateListener::EXTRA_PROPERTY] ?? null;

        return is_string($declared) && '' !== $declared ? $declared : null;
    }

    /**
     * @return null|string "public", or the IRI of the resource that the response delegates its security to
     */
    public function resolveDelegate(Request $request): ?string {
        $declared = $this->getDeclaredDelegate($request);
        $operation = $this->getOperation($request);
        if (null === $declared || CacheSecurityDelegateListener::DELEGATE_PUBLIC === $declared || null === $operation) {
            return $declared;
        }

        $delegate = $this->expressionLanguage->evaluate($declared, $this->getVariables($request, $operation));
        if (null === $delegate) {
            return null;
        }

        if (!is_object($delegate)) {
            throw new \LogicException("The cache security delegate of {$operation->getName()} must evaluate to a resource.");
        }

        return $this->iriConverter->getIriFromResource($delegate);
    }

    public function getOperation(Request $request): ?Operation {
        if (!RequestAttributesExtractor::extractAttributes($request)) {
            return null;
        }

        return $this->initializeOperation($request);
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
