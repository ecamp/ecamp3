<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PeriodRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PeriodRepository::class)
 */
#[ApiResource]
class Period extends BaseEntity {
    /**
     * @ORM\ManyToOne(targetEntity=Camp::class, inversedBy="periods")
     * @ORM\JoinColumn(nullable=false)
     */
    public ?Camp $camp = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public ?string $description = null;

    /**
     * @ORM\Column(type="date")
     */
    public ?DateTimeInterface $start = null;

    /**
     * @ORM\Column(name="`end`", type="date")
     */
    public ?DateTimeInterface $end = null;
}
