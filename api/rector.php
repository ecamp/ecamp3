<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\ClassMethod\LocallyCalledStaticMethodToNonStaticRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfElseToTernaryRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\AddInstanceofAssertForNullableArgumentRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\AddInstanceofAssertForNullableInstanceRector;
use Rector\PHPUnit\CodeQuality\Rector\ClassMethod\NoSetupWithParentCallOverrideRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\AssertEmptyNullableObjectToAssertInstanceofRector;
use Rector\PHPUnit\CodeQuality\Rector\StmtsAwareInterface\DeclareStrictTypesTestsRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\AllowMockObjectsWhereParentClassRector;
use Rector\PHPUnit\PHPUnit120\Rector\Class_\AllowMockObjectsWithoutExpectationsAttributeRector;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Symfony\Symfony73\Rector\Class_\ConstraintOptionsToNamedArgumentsRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

// @noinspection PhpUnhandledExceptionInspection
return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/public',
        __DIR__.'/config',
        __DIR__.'/migrations',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withComposerBased(doctrine: true, phpunit: true, symfony: true)
    ->withPreparedSets(deadCode: true, codeQuality: true, privatization: true, rectorPreset: true, phpunitCodeQuality: true, symfonyCodeQuality: true)
    ->withAttributesSets(all: true)
    ->withPhpSets(php84: true)
    ->withConfiguredRule(RenameFunctionRector::class, [
        'implode' => 'join',
        'join' => 'join',
    ])
    ->withSkip([
        AddInstanceofAssertForNullableArgumentRector::class,
        AddInstanceofAssertForNullableInstanceRector::class,
        AllowMockObjectsWhereParentClassRector::class,
        AllowMockObjectsWithoutExpectationsAttributeRector::class,
        AssertEmptyNullableObjectToAssertInstanceofRector::class,
        ClosureToArrowFunctionRector::class,
        CombineIfRector::class,
        ConstraintOptionsToNamedArgumentsRector::class,
        DeclareStrictTypesRector::class,
        DeclareStrictTypesTestsRector::class,
        DisallowedEmptyRuleFixerRector::class,
        ExplicitBoolCompareRector::class,
        FinalizeTestCaseClassRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        LocallyCalledStaticMethodToNonStaticRector::class,
        NoSetupWithParentCallOverrideRector::class,
        NullToStrictStringFuncCallArgRector::class,
        PreferPHPUnitThisCallRector::class,
        RemoveDeadInstanceOfRector::class,
        RemoveParentDelegatingConstructorRector::class,
        SimplifyIfElseToTernaryRector::class,
        SimplifyIfReturnBoolRector::class,
        ThrowWithPreviousExceptionRector::class,
        UseIdenticalOverEqualWithSameTypeRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__.'/src/DTO/*',
        ],
    ])
;
