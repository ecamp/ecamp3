<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\InputFilter;

/**
 * Edits a property in the payload submitted by an API user.
 *
 * An input filter can be defined on a property.
 * The InputFilter class encapsulates all the configuration required for
 * filtering this property successfully.
 *
 * InputFilter instances are immutable and serializable.
 */
abstract class InputFilter {

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

    /**
     * This method should filter the given property in the given array, and
     * return the mutated data.
     *
     * @param array $data input data to be filtered
     * @param string $propertyName property name of property inside array to be filtered
     * @return array filtered data
     */
    abstract function applyTo(array $data, string $propertyName): array;

    public function getPriority(): int {
        return $this->priority;
    }

    /**
     * @throws InvalidOptionsException This magic method is only called if
     *                                 an invalid option name is given
     */
    public function __set($name, $value) {
        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in input filter "%s".', $name, static::class), [$name]);
    }
}
