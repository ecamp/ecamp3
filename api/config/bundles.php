<?php

use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Exercise\HTMLPurifierBundle\ExerciseHTMLPurifierBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use FOS\HttpCacheBundle\FOSHttpCacheBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
use KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;
use Sentry\SentryBundle\SentryBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

return [
    FrameworkBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    TwigBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    ApiPlatformBundle::class => ['all' => true],
    NelmioCorsBundle::class => ['all' => true],
    WebProfilerBundle::class => ['dev' => true, 'test' => true, 'performance_test' => true],
    MakerBundle::class => ['dev' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    DebugBundle::class => ['dev' => true, 'test' => true, 'performance_test' => true],
    StofDoctrineExtensionsBundle::class => ['all' => true],
    LexikJWTAuthenticationBundle::class => ['all' => true],
    NelmioAliceBundle::class => ['dev' => true, 'test' => true, 'performance_test' => true],
    FidryAliceDataFixturesBundle::class => ['dev' => true, 'test' => true, 'performance_test' => true],
    HautelookAliceBundle::class => ['dev' => true, 'test' => true, 'performance_test' => true],
    ExerciseHTMLPurifierBundle::class => ['all' => true],
    KnpUOAuth2ClientBundle::class => ['all' => true],
    SentryBundle::class => ['all' => true],
    TwigExtraBundle::class => ['all' => true],
    FOSHttpCacheBundle::class => ['all' => true],
];
