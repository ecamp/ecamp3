<?php

namespace App\InputFilter;

/**
 * Edits a property in the payload submitted by an API user.
 *
 * In case your derived input filter class does not match the
 * pattern App\InputFilter\*Filter, you will need to configure
 * it as a service and tag it with 'app.input_filter'.
 */
abstract class InputFilter {
    protected FilterAttribute $filterAttribute;

    /**
     * @throws InvalidOptionsException This magic method is only called if
     *                                 an invalid option name is given
     */
    public function __set($name, $value) {
        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in input filter "%s".', $name, static::class), [$name]);
    }

    /**
     * This method should filter the given property in the given array, and
     * return the mutated data.
     *
     * @param array  $data         input data to be filtered
     * @param string $propertyName property name of property inside array to be filtered
     *
     * @return array filtered data
     */
    abstract public function applyTo(array $data, string $propertyName): array;

    public function setFilterAttribute(FilterAttribute $filterAttribute): void {
        $this->filterAttribute = $filterAttribute;
    }
}
