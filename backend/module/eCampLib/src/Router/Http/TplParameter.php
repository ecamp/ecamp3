<?php


namespace eCamp\Lib\Router\Http;


class TplParameter {
    protected $value;

    public function __construct($value) {
        $this->value = $value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function __toString() {
        return $this->getValue();
    }
}