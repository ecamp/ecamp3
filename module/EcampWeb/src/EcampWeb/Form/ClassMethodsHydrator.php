<?php
namespace EcampWeb\Form;

use Zend\Stdlib\Hydrator\ClassMethods;

class ClassMethodsHydrator extends ClassMethods
{
    /**
     * Define if extract values will use camel case or name with underscore
     * @param bool|array $underscoreSeparatedKeys
     */
    public function __construct($underscoreSeparatedKeys = true)
    {
        parent::__construct($underscoreSeparatedKeys);
    }

    /**
     * Disable hydrating
     * This makes it a readonly/extract-only hydrator
     *
     * @param  array                            $data
     * @param  object                           $object
     * @return object
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function hydrate(array $data, $object)
    {
        return $object;
    }
}
