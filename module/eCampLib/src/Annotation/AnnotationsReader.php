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

    /**
     * @return Reader
     * @throws AnnotationException
     */
    private static function getReader() {
        if (self::$reader == null) {
            $cache = new ArrayCache();
            $reader = new AnnotationReader();

            self::$reader = new CachedReader($reader, $cache);
        }
        return self::$reader;
    }


    /** @var ArrayCache */
    private static $refClassCache = null;

    /**
     * @param $class
     * @return \ReflectionClass
     * @throws ReflectionException
     */
    private static function getRefClass($class) {
        if (self::$refClassCache == null) {
            self::$refClassCache = new ArrayCache();
        }

        $refClass = self::$refClassCache->fetch($class);

        if ($refClass === false) {
            $refClass = new \ReflectionClass($class);
            self::$refClassCache->save($class, $refClass);
        }

        return $refClass;
    }


    /**
     * @param $class
     * @param $name
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
     * @return EntityFilter
     */
    public static function getEntityFilterAnnotation($class) {
        /** @var EntityFilter $entityFilter */
        $entityFilter = self::getClassAnnotation($class, EntityFilter::class);
        return $entityFilter;
    }
}
