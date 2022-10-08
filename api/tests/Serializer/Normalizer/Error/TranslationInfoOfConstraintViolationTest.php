<?php

namespace App\Tests\Serializer\Normalizer\Error;

use App\Entity\CampCollaboration;
use App\Serializer\Normalizer\Error\TranslationInfoOfConstraintViolation;
use App\Validator\AllowTransition\AssertAllowTransitions;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @internal
 */
class TranslationInfoOfConstraintViolationTest extends TestCase {
    private TranslationInfoOfConstraintViolation $translationInfoOfConstraintViolation;

    protected function setUp(): void {
        $this->translationInfoOfConstraintViolation = new TranslationInfoOfConstraintViolation();
    }

    /**
     * @dataProvider constraintViolations()
     */
    public function testExtractsTranslationInfoFromConstraintViolation(ConstraintViolation $violation, string $key): void {
        $translationInfo = $this->translationInfoOfConstraintViolation->extract($violation);
        $parametersWithoutCurlyBraces = TranslationInfoOfConstraintViolation::removeCurlyBraces($violation->getParameters());

        assertThat($translationInfo->key, equalTo($key));
        assertThat($translationInfo->parameters, equalTo($parametersWithoutCurlyBraces));
    }

    public function constraintViolations(): array {
        return [
            AssertAllowTransitions::class => [
                'violation' => new ConstraintViolation(
                    message: 'value must be one of inactive, was established',
                    messageTemplate: 'value must be one of {{ to }}, was {{ value }}',
                    parameters: ['{{ to }}' => 'inactive', '{{ value }}' => 'established'],
                    root: new CampCollaboration(),
                    propertyPath: 'status',
                    invalidValue: 'established',
                    plural: null,
                    code: null,
                    constraint: new AssertAllowTransitions(transitions: [])
                ),
                'key' => 'app.validator.allowtransition.assertallowtransitions',
            ],
            NotBlank::class => [
                'violation' => new ConstraintViolation(
                    message: 'This value should not be blank.',
                    messageTemplate: 'This value should not be blank.',
                    parameters: ['{{ value }}' => '""'],
                    root: new CampCollaboration(),
                    propertyPath: 'name',
                    invalidValue: '',
                    plural: null,
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                    constraint: new NotBlank()
                ),
                'key' => 'symfony.component.validator.constraints.notblank',
            ],
            NotBlank::class.' without parameters' => [
                'violation' => new ConstraintViolation(
                    message: 'This value should not be blank.',
                    messageTemplate: 'This value should not be blank.',
                    parameters: [],
                    root: new CampCollaboration(),
                    propertyPath: 'name',
                    invalidValue: '',
                    plural: null,
                    code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                    constraint: new NotBlank()
                ),
                'key' => 'symfony.component.validator.constraints.notblank',
            ],
        ];
    }
}
