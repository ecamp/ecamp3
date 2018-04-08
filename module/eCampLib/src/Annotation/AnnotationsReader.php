<?php

namespace eCamp\Lib\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;

class AnnotationsReader
{

    /** @var Reader */
    private static $reader = null;

    private static function getReader() {
        if (self::$reader == null) {
            $cache = new ArrayCache();
            $reader = new AnnotationReader();

            self::$reader = new CachedReader($reader, $cache);
        }
        return self::$reader;
    }


    private static $refClassCache = null;

    private static function getRefClass($class) {
        if (self::$refClassCache == null) {
            self::$refClassCache = new ArrayCache();
        }

        $refClass = self::$refClassCache->fetch($class);

        if($refClass === false) {
            $refClass = new \ReflectionClass($class);
            self::$refClassCache->save($class, $refClass);
        }

        return $refClass;
    }


    public static function getClassAnnotation($class, $name) {
        $refClass = self::getRefClass($class);
        return self::getReader()->getClassAnnotation($refClass, $name);
    }

    public static function getEntityFilterAnnotation($class) {
        return self::getClassAnnotation($class, EntityFilter::class);
    }

}
