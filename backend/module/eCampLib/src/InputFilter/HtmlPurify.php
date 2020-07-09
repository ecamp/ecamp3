<?php

namespace eCamp\Lib\InputFilter;

use HTMLPurifier;
use Laminas\Filter\AbstractFilter;

class HtmlPurify extends AbstractFilter {
    /** @var HTMLPurifier */
    private $htmlPurifier;

    public function __construct(HTMLPurifier $htmlPurifier = null) {
        $this->htmlPurifier = $htmlPurifier ?: new HTMLPurifier();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function filter($value) {
        return $this->htmlPurifier->purify($value);
    }
}
