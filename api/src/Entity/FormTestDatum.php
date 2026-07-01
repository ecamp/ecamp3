<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A single shared row used to exercise the API-backed form components from
 * tests (e.g. Playwright). There is exactly one instance and every
 * authenticated user reads and writes that same row, which makes it possible to
 * change a value in one browser tab and observe the change after reloading in
 * another tab. No create/delete operations are exposed.
 */
#[ApiResource(
    operations: [
        new Get(
            security: 'is_authenticated()'
        ),
        new GetCollection(
            security: 'is_authenticated()'
        ),
        new Patch(
            security: 'is_authenticated()'
        ),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[ORM\Entity]
class FormTestDatum extends BaseEntity {
    /**
     * Single-line text, for ApiTextField / ApiSelect.
     */
    #[ApiProperty(example: 'Hello')]
    #[Groups(['read', 'write'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $text = null;

    /**
     * Multi-line text, for ApiTextarea.
     */
    #[ApiProperty(example: "Line one\nLine two")]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $multilineText = null;

    /**
     * HTML, for ApiRichtext.
     */
    #[ApiProperty(example: '<p>Rich <strong>text</strong></p>')]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $html = null;

    /**
     * Whole number, for ApiNumberField.
     */
    #[ApiProperty(example: 42)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $number = null;

    /**
     * Boolean, for ApiCheckbox / ApiSwitch.
     */
    #[ApiProperty(example: true)]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $flag = false;

    /**
     * Hex color string, for ApiColorField / ApiColorPicker.
     */
    #[ApiProperty(example: '#1976d2')]
    #[Groups(['read', 'write'])]
    #[Assert\Length(max: 9)]
    #[ORM\Column(type: 'string', length: 9, nullable: true)]
    public ?string $color = null;

    /**
     * ISO date string (YYYY-MM-DD), for ApiDatePicker.
     */
    #[ApiProperty(example: '2024-01-15')]
    #[Groups(['read', 'write'])]
    #[Assert\Length(max: 10)]
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    public ?string $date = null;

    /**
     * Time of day, for ApiTimePicker.
     *
     * Stored as a datetime (in UTC) and exchanged as an ISO 8601 string
     * (e.g. "2024-01-15T09:30:00+00:00"), which the ApiTimePicker / ETimePicker
     * read and write with their default valueFormat "YYYY-MM-DDTHH:mm:ssZ".
     */
    #[ApiProperty(example: '2024-01-15T09:30:00+00:00')]
    #[Context(
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP'],
        denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP']
    )]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\DateTime $time = null;

    /**
     * Single locale string (e.g. "en"), for a single-value ApiSelect.
     */
    #[ApiProperty(example: 'en')]
    #[Groups(['read', 'write'])]
    #[Assert\Length(max: 16)]
    #[ORM\Column(type: 'string', length: 16, nullable: true)]
    public ?string $language = null;

    /**
     * List of locale strings (e.g. ["en", "de"]), for a multiple ApiSelect.
     *
     * @var string[]
     */
    #[ApiProperty(example: ['en', 'de'])]
    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'json')]
    public array $languageMultiselect = [];
}
