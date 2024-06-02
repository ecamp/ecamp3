<?php

namespace App\DTO;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use App\InputFilter;
use App\State\ResendActivationProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Post(
            processor: ResendActivationProcessor::class,
            uriTemplate: '/resend_activation{._format}',
            security: 'true',
            status: 204,
            output: false,
            denormalizationContext: ['groups' => ['create']],
            openapi: new OpenApiOperation(summary: 'Request activation email again', description: 'Activation email will be sent to the given email again.')
        ),
    ],
    routePrefix: '/auth'
)]
class UserActivation {
    #[InputFilter\Trim]
    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['create'])]
    public ?string $email = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Groups(['create'])]
    public ?string $recaptchaToken = null;
}
