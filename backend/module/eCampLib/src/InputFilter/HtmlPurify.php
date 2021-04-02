<?php

namespace eCamp\Lib\InputFilter;

use HTMLPurifier;
use Laminas\Filter\AbstractFilter;

class HtmlPurify extends AbstractFilter {
    private ?HTMLPurifier $htmlPurifier = null;

    public function __construct($options = null) {
        if ($options) {
            $this->options = $options;
        } else {
            $this->options = [
                'Cache.SerializerPath' => __DIR__.'/../../../../data/HTMLPurifier/Serializer',
            ];
        }
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
