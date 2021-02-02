<?php

namespace eCamp\Core\Service;

class Invitation {
    private string $campId;
    private string $campTitle;
    private ?string $userDisplayName;
    private ?bool $userAlreadyInCamp;

    public function __construct(string $campId, string $campTitle, ?string $userDisplayName, ?bool $userAlreadyInCamp) {
        $this->campId = $campId;
        $this->campTitle = $campTitle;
        $this->userDisplayName = $userDisplayName;
        $this->userAlreadyInCamp = $userAlreadyInCamp;
    }

    public function getCampId(): string {
        return $this->campId;
    }

    public function getCampTitle(): string {
        return $this->campTitle;
    }

    public function getUserDisplayName(): ?string {
        return $this->userDisplayName;
    }

    public function isUserAlreadyInCamp(): ?bool {
        return $this->userAlreadyInCamp;
    }
}
