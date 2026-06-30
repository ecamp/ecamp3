<?php

declare(strict_types=1);

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\InputFilter;
use App\Security\PwnedPasswords\NotContextSpecificPassword;
use App\State\ResetPasswordCreateProcessor;
use App\State\ResetPasswordProvider;
use App\State\ResetPasswordUpdateProcessor;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/reset_password/{id}{._format}', // TO DISCUSS: default uri would be /reset_password (plural). Shall we keep or fix?
            security: 'true',
            provider: ResetPasswordProvider::class,
        ),
        new Patch(
            uriTemplate: '/reset_password/{id}{._format}',
            denormalizationContext: ['groups' => ['update']],
            security: 'true',
            provider: ResetPasswordProvider::class,
            processor: ResetPasswordUpdateProcessor::class
        ),
        new Post(
            uriTemplate: '/reset_password{._format}',
            status: 204,
            openapi: new OpenApiOperation(summary: 'Request Password-Reset-Mail', description: 'Password-Reset-Link will be sent to the given email'),
            normalizationContext: ['groups' => ['read']],
            denormalizationContext: ['groups' => ['create']],
            security: 'true',
            output: false,
            processor: ResetPasswordCreateProcessor::class
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

    #[InputFilter\Trim]
    #[ApiProperty(readable: true, writable: true)]
    #[Groups(['create', 'read'])]
    public ?string $email = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['create', 'update'])]
    public ?string $recaptchaToken = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['update'])]
    #[Assert\Length(min: 12, max: 128)]
    #[NotContextSpecificPassword]
    public ?string $password = null;
}
