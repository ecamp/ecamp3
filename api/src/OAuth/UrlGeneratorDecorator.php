<?php

namespace App\OAuth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class UrlGeneratorDecorator implements UrlGeneratorInterface {
    public function __construct(
        private readonly UrlGeneratorInterface $decorated,
        private readonly string $env
    ) {}

    #[\Override]
    public function setContext(RequestContext $context): void {
        $this->decorated->setContext($context);
    }

    #[\Override]
    public function getContext(): RequestContext {
        return $this->decorated->getContext();
    }

    #[\Override]
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string {
        $url = $this->decorated->generate($name, $parameters, $referenceType);
        if ('prod' === $this->env) {
            $url = preg_replace('/^http:\/\//', 'https://', $url);
            if (is_null($url)) {
                throw new \Exception('Unexpected redirect URI');
            }
        }

        return $url;
    }
}
