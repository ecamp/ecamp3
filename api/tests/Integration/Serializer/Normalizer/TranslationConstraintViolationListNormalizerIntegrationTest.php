<?php

namespace App\Tests\Integration\Serializer\Normalizer;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestAssertionsTrait;
use ApiPlatform\Core\Problem\Serializer\ConstraintViolationListNormalizer;
use App\Entity\CampCollaboration;
use App\Serializer\Normalizer\TranslationConstraintViolationListNormalizer;
use App\Validator\AllowTransition\AssertAllowTransitions;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 */
class TranslationConstraintViolationListNormalizerIntegrationTest extends KernelTestCase {
    use ApiTestAssertionsTrait;

    private TranslationConstraintViolationListNormalizer $translationConstraintViolationListNormalizer;

    /**
     * @throws \Exception
     */
    protected function setUp(): void {
        self::bootKernel();
        parent::setUp();

        /** @var TranslationConstraintViolationListNormalizer $obj */
        $obj = self::getContainer()->get('api_platform.problem.normalizer.constraint_violation_list');
        $this->translationConstraintViolationListNormalizer = $obj;
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function testAddsTranslationKeyAndParameters() {
        $constraintViolationList = new ConstraintViolationList(self::getConstraintViolations());

        $result = $this->translationConstraintViolationListNormalizer->normalize(
            $constraintViolationList,
            ConstraintViolationListNormalizer::FORMAT,
            []
        );

        self::assertArraySubset(['violations' => [
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
        ]], $result);
    }

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function testAddsTranslations() {
        $constraintViolationList = new ConstraintViolationList(self::getConstraintViolations());

        $result = $this->translationConstraintViolationListNormalizer->normalize(
            $constraintViolationList,
            ConstraintViolationListNormalizer::FORMAT,
            []
        );

        self::assertArraySubset(['violations' => [
            [
                'i18n' => [
                    'translations' => [
                        'en' => 'value must be one of inactive, was established',
                        'de' => 'Der Wert muss einer aus inactive sein, aber er war established',
                        'fr' => 'La valeur doit être l\'une des suivantes : inactive, a été established',
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
        ]], $result);
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
        ];
    }
}
