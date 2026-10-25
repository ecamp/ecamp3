<?php

namespace App\Tests\Integration\State;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\Test\ApiTestAssertionsTrait;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Entity\CampCollaboration;
use App\State\ValidationErrorProvider;
use App\Validator\AllowTransition\AssertAllowTransitions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 */
class ValidationErrorProviderIntegrationTest extends KernelTestCase {
    use ApiTestAssertionsTrait;

    private ValidationErrorProvider $validationErrorProvider;

    /**
     * @throws \Exception
     */
    protected function setUp(): void {
        self::bootKernel();
        parent::setUp();

        /** @var ValidationErrorProvider $obj */
        $obj = self::getContainer()->get(ValidationErrorProvider::class);
        $this->validationErrorProvider = $obj;
    }

    public function testAddsTranslationKeyAndParameters() {
        $constraintViolationList = new ConstraintViolationList(self::getConstraintViolations());

        $request = new Request();
        $validationException = new ValidationException(message: $constraintViolationList, code: 0);
        $request->attributes->set('exception', $validationException);

        $validationError = $this->validationErrorProvider->provide(
            new Patch(status: 422),
            [],
            [
                'request' => $request,
            ]
        );

        self::assertArraySubset([
            [
                'i18n' => [
                    'key' => 'app.validator.allowtransition.assertallowtransitions',
                    'parameters' => [
                        'to' => 'inactive',
                        'value' => 'established',
                    ],
                ],
            ],
            [
                'i18n' => [
                    'key' => 'symfony.component.validator.constraints.notblank',
                    'parameters' => [
                        'value' => '""',
                    ],
                ],
            ],
            [
                'i18n' => [
                    'key' => 'symfony.component.validator.constraints.notnull',
                    'parameters' => [],
                ],
            ],
            [
                'i18n' => [
                    'key' => 'app.tests.integration.state.myconstraint',
                    'parameters' => [],
                ],
            ],
        ], $validationError->getViolations());
    }

    public function testAddsTranslations() {
        $constraintViolationList = new ConstraintViolationList(self::getConstraintViolations());

        $request = new Request();
        $validationException = new ValidationException(message: $constraintViolationList, code: 0);
        $request->attributes->set('exception', $validationException);

        $validationError = $this->validationErrorProvider->provide(
            new Patch(status: 422),
            [],
            [
                'request' => $request,
            ]
        );

        self::assertArraySubset([
            [
                'i18n' => [
                    'translations' => [
                        'en' => 'value must be one of inactive, was established',
                        'de' => 'Wert muss einer von inactive sein, war established',
                        'fr' => 'la valeur "established" doit être l\'une des valeurs suivantes : inactive',
                        'it' => 'deve essere uno dei seguenti valori: inactive, è established',
                    ],
                ],
            ],
            [
                'i18n' => [
                    'translations' => [
                        'en' => 'This value should not be blank.',
                        'de' => 'Dieser Wert sollte nicht leer sein.',
                        'fr' => 'Cette valeur ne doit pas être vide.',
                        'it' => 'Questo valore non dovrebbe essere vuoto.',
                    ],
                ],
            ],
            [
                'i18n' => [
                    'translations' => [
                        'en' => 'This value should not be null.',
                        'de' => 'Dieser Wert sollte nicht null sein.',
                        'fr' => 'Cette valeur ne doit pas être nulle.',
                        'it' => 'Questo valore non dovrebbe essere nullo.',
                    ],
                ],
            ],
            [
                'i18n' => [
                    'translations' => [
                        'en' => 'en',
                        'en_CH_scout' => 'en_CH_scout',
                        'de' => 'de',
                        'de_CH_scout' => 'de_CH_scout',
                        'fr' => 'fr',
                        'fr_CH_scout' => 'fr_CH_scout',
                        'it' => 'it',
                        'it_CH_scout' => 'it_CH_scout',
                        'rm' => 'rm',
                        'rm_CH_scout' => 'rm_CH_scout',
                    ],
                ],
            ],
        ], $validationError->getViolations());
    }

    public static function getConstraintViolations(): array {
        return [
            new ConstraintViolation(
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
            new ConstraintViolation(
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
            new ConstraintViolation(
                message: 'This value should not be null.',
                messageTemplate: 'This value should not be null.',
                parameters: [],
                root: new CampCollaboration(),
                propertyPath: 'name',
                invalidValue: '',
                plural: null,
                code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc3',
                constraint: new NotNull()
            ),
            new ConstraintViolation(
                message: 'This is a test message for i18n variants',
                messageTemplate: 'This is a test message for i18n variants',
                parameters: [],
                root: new CampCollaboration(),
                propertyPath: 'name',
                invalidValue: '',
                plural: null,
                code: 'c1051bb4-d103-4f74-8988-acbcafc7fdc5',
                constraint: new MyConstraint()
            ),
        ];
    }
}

class MyConstraint extends Constraint {
    public function __construct(
        ?array $options = null,
        ?array $groups = null,
        $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);
    }
}
