<?php

namespace eCamp\Lib\InputFilter;

use HTMLPurifier;
use Laminas\Filter\AbstractFilter;

class HtmlPurify extends AbstractFilter {
    /** @var HTMLPurifier */
    private $htmlPurifier;

    public function __construct($options = null) {
        $this->options = $options;
    }

    public function getHtmlPurifier() {
        if (null == $this->htmlPurifier) {
            $this->htmlPurifier = new HTMLPurifier($this->options);
        }

        return $this->htmlPurifier;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value) {
        return $this->getHtmlPurifier()->purify($value);
    }
}
