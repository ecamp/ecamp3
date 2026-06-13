<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\OpenApi;

final readonly class RefreshTokenDecorator implements OpenApiFactoryInterface {
    public function __construct(private OpenApiFactoryInterface $decorated, private string $cookiePrefix) {}

    #[\Override]
    public function __invoke(array $context = []): OpenApi {
        $openApi = ($this->decorated)($context);

        $cookiePrefix = $this->cookiePrefix;
        $pathItem = new PathItem(
            ref: 'JWT Token Refresh',
            post: new Operation(
                operationId: 'postRefreshToken',
                tags: ['JWT Refresh'],
                responses: [
                    '204' => new Response(
                        description: "Get a refreshed JWT token split across the two cookies {$cookiePrefix}jwt_hp and {$cookiePrefix}jwt_s. Also returns a new refresh token {$cookiePrefix}refresh_token.",
                    ),
                ],
                summary: 'Refresh token.',
            ),
        );
        $openApi->getPaths()->addPath('/token/refresh', $pathItem);

        return $openApi;
    }
}
