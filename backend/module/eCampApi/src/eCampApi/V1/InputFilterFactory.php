<?php

namespace eCampApi\V1;

class InputFilterFactory {
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
    ): InputFilterFactory {
        return new InputFilterFactory($name, $required);
    }

    public function setRequired(bool $required = true): InputFilterFactory {
        $this->required = $required;

        return $this;
    }

    public function addFilters(...$filters): InputFilterFactory {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    public function addFilter($filter): InputFilterFactory {
        if (is_string($filter)) {
            $filter = ['name' => $filter];
        }
        $this->filters[] = $filter;

        return $this;
    }

    public function addFilterStringTrim(): InputFilterFactory {
        return $this->addFilter(\Laminas\Filter\StringTrim::class);
    }

    public function addFilterStripTags(): InputFilterFactory {
        return $this->addFilter(\Laminas\Filter\StripTags::class);
    }

    public function addValidators(...$validators): InputFilterFactory {
        foreach ($validators as $validator) {
            $this->addValidator($validator);
        }

        return $this;
    }

    public function addValidator($validator): InputFilterFactory {
        if (is_string($validator)) {
            $validator = ['name' => $validator];
        }
        $this->validators[] = $validator;

        return $this;
    }

    public function addValidatorStringLength($min, $max): InputFilterFactory {
        return $this->addValidator([
            'name' => \Laminas\Validator\StringLength::class,
            'options' => ['min' => $min, 'max' => $max],
        ]);
    }

    public function addValidatorIsFloat(): InputFilterFactory {
        return $this->addValidator(\Laminas\Validator\Digits::class);
    }

    public function addValidatorRegex($pattern): InputFilterFactory {
        return $this->addValidator([
            'name' => \Laminas\Validator\Regex::class,
            'options' => ['pattern' => $pattern],
        ]);
    }

    public function addValidatorInArray(array $haystack): InputFilterFactory {
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
