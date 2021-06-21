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
    private ?Camp $camp = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $start = null;

    /**
     * @ORM\Column(name="`end`", type="date")
     */
    private ?DateTimeInterface $end = null;

    public function getCamp(): ?Camp {
        return $this->camp;
    }

    public function setCamp(?Camp $camp): self {
        $this->camp = $camp;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?DateTimeInterface {
        return $this->start;
    }

    public function setStart(DateTimeInterface $start): self {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?DateTimeInterface {
        return $this->end;
    }

    public function setEnd(DateTimeInterface $end): self {
        $this->end = $end;

        return $this;
    }
}
