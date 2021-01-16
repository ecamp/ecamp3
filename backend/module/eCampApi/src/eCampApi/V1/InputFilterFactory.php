<?php

namespace eCampApi\V1;

class InputFilterFactory {
    private $config;
    private $name;
    private $required;
    private $filters = [];
    private $validators = [];

    public function __construct(
        ConfigFactory $config,
        string $name,
        bool $required
    ) {
        $this->config = $config;
        $this->name = $name;
        $this->required = $required;
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

    public function addValidatorRegex($pattern): InputFilterFactory {
        return $this->addValidator([
            'name' => \Laminas\Validator\Regex::class,
            'options' => ['pattern' => $pattern],
        ]);
    }

    public function buildInputFilter(): ConfigFactory {
        $this->config->addInputFilterItem(
            $this->name,
            $this->required,
            $this->filters,
            $this->validators
        );

        return $this->config;
    }
}
