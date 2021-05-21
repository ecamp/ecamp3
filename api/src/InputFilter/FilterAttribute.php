<?php

namespace App\InputFilter;

/**
 * Base class for input filter attributes.
 *
 * An input filter attribute can be defined on a property.
 * The FilterAttribute class encapsulates all the configuration required for
 * filtering this property successfully.
 *
 * FilterAttribute instances are immutable and serializable.
 */
abstract class FilterAttribute {

    protected int $priority;

    /**
     * Initializes the input filter with options.
     *
     * You should pass an associative array. The keys should be the names of
     * existing properties in this class. The values should be the value for these
     * properties.
     *
     * @param array $options The options (as associative array)
     * @param int $priority The priority of this input filter. Higher priorities are executed first.
     *                      Priorities are evaluated for the whole entity class at once.
     */
    public function __construct(array $options = [], int $priority = 0) {
        $this->priority = $priority;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
    }

    public function getPriority(): int {
        return $this->priority;
    }

    /**
     * Override this to customize the filter logic implementation class.
     *
     * In case your filter logic class does not match the pattern
     * App\InputFilter\*Filter, you will need to configure it as a
     * service and tag it with 'app.input_filter'.
     *
     * @return string the class name of the filtering logic class responsible for properties with this attribute
     */
    public function filteredBy(): string {
        return static::class.'Filter';
    }

    /**
     * @throws InvalidOptionsException This magic method is only called if
     *                                 an invalid option name is given
     */
    public function __set($name, $value) {
        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in input filter "%s".', $name, static::class), [$name]);
    }
}
