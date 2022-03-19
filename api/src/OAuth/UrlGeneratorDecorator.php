<?php

namespace App\OAuth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class UrlGeneratorDecorator implements UrlGeneratorInterface
{
    public function __construct(
        private UrlGeneratorInterface $decorated,
        private string $env
    ) {
    }

    public function setContext(RequestContext $context)
    {
        return $this->decorated->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->decorated->getContext();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $url = $this->decorated->generate($name, $parameters, $referenceType);
        if ($this->env === 'prod') {
            $url = preg_replace('/^http:\/\//', 'https://', $url);
            if (is_null($url)) {
                throw new \Exception('Unexpected redirect URI');
            }
        }
        return $url;
    }
}
