<?php

namespace App\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'post' => [
            'security' => 'true',
            'path' => '',
            'status' => 204,
            'denormalization_context' => ['groups' => ['create']],
            'normalization_context' => ['groups' => ['read']],
            'openapi_context' => [
                'summary' => 'Request Password-Reset-Mail',
                'description' => 'Password-Reset-Link will be sent to the given email',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'true',
            'path' => '/{id}',
        ],
        'patch' => [
            'security' => 'true',
            'path' => '/{id}',
            'denormalization_context' => ['groups' => ['update']],
        ],
    ],
    routePrefix: '/auth/reset_password'
)]
class ResetPassword {
    /**
     * $id: base64_encode($email . '#' . $resetKey).
     */
    #[ApiProperty(readable: true, writable: false, identifier: true)]
    #[Groups(['read'])]
    public ?string $id = null;

    #[ApiProperty(readable: true, writable: true)]
    #[Groups(['create', 'read'])]
    public ?string $email = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['create', 'update'])]
    public ?string $recaptchaToken = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['update'])]
    public ?string $password = null;
}
