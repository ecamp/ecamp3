<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

/**
 * Decorates the OpenApi factory to add API docs for the login endpoint.
 */
final class JwtDecorator implements OpenApiFactoryInterface {
    public function __construct(private OpenApiFactoryInterface $decorated, private string $cookiePrefix) {
    }

    public function __invoke(array $context = []): OpenApi {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'identifier' => [
                    'type' => 'string',
                    'example' => 'test@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'test',
                ],
            ],
        ]);

        $cookiePrefix = $this->cookiePrefix;
        $pathItem = new Model\PathItem(
            ref: 'JWT Token',
            post: new Model\Operation(
                operationId: 'postCredentials',
                tags: ['Login'],
                responses: [
                    '204' => [
                        'description' => "Get a JWT token split across the two cookies {$cookiePrefix}jwt_hp and {$cookiePrefix}jwt_s",
                    ],
                ],
                summary: 'Log in using email and password.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token by logging in',
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );
        $openApi->getPaths()->addPath('/authentication_token', $pathItem);

        return $openApi;
    }
}
