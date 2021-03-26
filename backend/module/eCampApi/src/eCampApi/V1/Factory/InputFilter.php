<?php

namespace eCampApi\V1\Factory;

class InputFilter {
    private $name;
    private $required;
    private $filters = [];
    private $validators = [];

    public function __construct(
        string $name,
        bool $required
    ) {
        $this->name = $name;
        $this->required = $required;
    }

    public static function Create(
        string $name,
        bool $required = false
    ): InputFilter {
        return new InputFilter($name, $required);
    }

    public function setRequired(bool $required = true): InputFilter {
        $this->required = $required;

        return $this;
    }

    public function addFilters(...$filters): InputFilter {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    public function addFilter($filter): InputFilter {
        if (is_string($filter)) {
            $filter = ['name' => $filter];
        }
        $this->filters[] = $filter;

        return $this;
    }

    public function addFilterStringTrim(): InputFilter {
        return $this->addFilter(\Laminas\Filter\StringTrim::class);
    }

    public function addFilterStripTags(): InputFilter {
        return $this->addFilter(\Laminas\Filter\StripTags::class);
    }

    public function addFilterDigits(): InputFilter {
        return $this->addFilter(\Laminas\Filter\Digits::class);
    }

    public function addValidators(...$validators): InputFilter {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    public function addValidator($validator): InputFilter {
        if (is_string($validator)) {
            $validator = ['name' => $validator];
        }
        $this->validators[] = $validator;

        return $this;
    }

    public function addValidatorStringLength($min, $max): InputFilter {
        return $this->addValidator([
            'name' => \Laminas\Validator\StringLength::class,
            'options' => ['min' => $min, 'max' => $max],
        ]);
    }

    public function addValidatorIsFloat(): InputFilter {
        return $this->addValidator(\Laminas\Validator\Digits::class);
    }

    public function addValidatorRegex($pattern): InputFilter {
        return $this->addValidator([
            'name' => \Laminas\Validator\Regex::class,
            'options' => ['pattern' => $pattern],
        ]);
    }

    public function addValidatorInArray(array $haystack): InputFilter {
        return $this->addValidator([
            'name' => \Laminas\Validator\InArray::class,
            'options' => ['haystack' => $haystack],
        ]);
    }

    public function build(): array {
        return [
            'name' => $this->name,
            'required' => $this->required,
            'filters' => $this->filters,
            'validators' => $this->validators,
        ];
    }
}
