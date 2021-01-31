<?php

namespace eCamp\Lib\Service;

class EntityValidationException extends \Exception {
    private array $messages = [];

    public function setMessages(array $messages): self {
        $this->messages = $messages;

        return $this;
    }

    public function getMessages(): array {
        return $this->messages;
    }
}
