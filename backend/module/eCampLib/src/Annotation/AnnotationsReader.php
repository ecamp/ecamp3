<?php

namespace eCamp\Lib\Annotation;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;
use Exception;
use ReflectionException;

class AnnotationsReader {
    /** @var Reader */
    private static $reader = null;

    /** @var ArrayCache */
    private static $refClassCache = null;

    /**
     * @param $class
     * @param $name
     *
     * @return object
     */
    public static function getClassAnnotation($class, $name) {
        try {
            $refClass = self::getRefClass($class);

            return self::getReader()->getClassAnnotation($refClass, $name);
        } catch (Exception $ex) {
            return null;
        }
    }

    /**
     * @param $class
     *
     * @return EntityFilter
     */
    public static function getEntityFilterAnnotation($class) {
        /** @var EntityFilter $entityFilter */
        return self::getClassAnnotation($class, EntityFilter::class);
    }

    /**
     * @throws AnnotationException
     *
     * @return Reader
     */
    private static function getReader() {
        if (null == self::$reader) {
            $cache = new ArrayCache();
            $reader = new AnnotationReader();

            self::$reader = new CachedReader($reader, $cache);
        }

        return self::$reader;
    }

    /**
     * @param $class
     *
     * @throws ReflectionException
     *
     * @return \ReflectionClass
     */
    private static function getRefClass($class) {
        if (null == self::$refClassCache) {
            self::$refClassCache = new ArrayCache();
        }

        $refClass = self::$refClassCache->fetch($class);

        if (false === $refClass) {
            $refClass = new \ReflectionClass($class);
            self::$refClassCache->save($class, $refClass);
        }

        return $refClass;
    }
}
