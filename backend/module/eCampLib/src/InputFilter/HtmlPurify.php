<?php

namespace eCamp\Lib\InputFilter;

use HTMLPurifier;
use Laminas\Filter\AbstractFilter;

class HtmlPurify extends AbstractFilter {
    private HTMLPurifier $htmlPurifier;

    public function __construct($options = null) {
        $this->options = $options;
    }

    public function getHtmlPurifier(): HTMLPurifier {
        if (null == $this->htmlPurifier) {
            $this->htmlPurifier = new HTMLPurifier($this->options);
        }

        return $this->htmlPurifier;
    }

    public function filter($value): string {
        return $this->getHtmlPurifier()->purify($value);
    }
}
