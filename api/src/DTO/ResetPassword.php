<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\State\ResetPasswordCreateProcessor;
use App\State\ResetPasswordProvider;
use App\State\ResetPasswordUpdateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            provider: ResetPasswordProvider::class,
            uriTemplate: '/reset_password/{id}{._format}', // TO DISCUSS: default uri would be /reset_password (plural). Shall we keep or fix?
            security: 'true',
        ),
        new Patch(
            provider: ResetPasswordProvider::class,
            processor: ResetPasswordUpdateProcessor::class,
            uriTemplate: '/reset_password/{id}{._format}',
            security: 'true',
            denormalizationContext: ['groups' => ['update']]
        ),
        new Post(
            processor: ResetPasswordCreateProcessor::class,
            uriTemplate: '/reset_password{._format}',
            security: 'true',
            status: 204,
            output: false,
            denormalizationContext: ['groups' => ['create']],
            normalizationContext: ['groups' => ['read']],
            openapiContext: ['summary' => 'Request Password-Reset-Mail', 'description' => 'Password-Reset-Link will be sent to the given email']
        ),
    ],
    routePrefix: '/auth'
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
