<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;

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
                'username' => [
                    'type' => 'string',
                    'example' => 'test-user',
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
                summary: 'Log in using username and password.',
                requestBody: new Model\RequestBody(
                    description: 'Generate new JWT Token by logging in',
                    content: new \ArrayObject([
                        'application/ld+json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                        'application/vnd.api+json' => [
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
