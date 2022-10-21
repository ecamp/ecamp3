<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            security: 'true',
            uriTemplate: '/{id}'
        ),
        new Patch(
            security: 'true',
            uriTemplate: '/{id}',
            denormalizationContext: ['groups' => ['update']]
        ),
        new Post(
            security: 'true',
            uriTemplate: '',
            status: 204,
            output: false,
            denormalizationContext: ['groups' => ['create']],
            normalizationContext: ['groups' => ['read']],
            openapiContext: ['summary' => 'Request Password-Reset-Mail', 'description' => 'Password-Reset-Link will be sent to the given email']
        ),
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
    #[Assert\Length(min: 12, max: 128)]
    public ?string $password = null;
}
