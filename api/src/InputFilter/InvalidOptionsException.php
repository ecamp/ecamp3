<?php

namespace App\InputFilter;

class InvalidOptionsException extends \RuntimeException {
    private array $options;

    public function __construct(string $message, array $options) {
        parent::__construct($message);

        $this->options = $options;
    }

    public function getOptions() {
        return $this->options;
    }
}
