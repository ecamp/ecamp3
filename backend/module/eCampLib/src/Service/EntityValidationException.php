<?php

namespace eCamp\Lib\Service;

class EntityValidationException extends \Exception {
    /** @var array */
    private $messages;

    public function setMessages(array $messages) {
        $this->messages = $messages;
    }

    public function getMessages() {
        return $this->messages;
    }
}
