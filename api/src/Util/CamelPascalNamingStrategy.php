<?php

namespace App\Util;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class CamelPascalNamingStrategy extends DefaultNamingStrategy {
    /**
     * Returns a table name for an entity class in snake_case
     * PascalCase is bad practice as many DB backend are not case sensitive.
     *
     * @param string $className the fully-qualified class name
     */
    public function classToTableName($className): string {
        return $this->classToSnakeCase($className);
    }

    /**
     * Returns a column name for an embedded property in camelCase.
     *
     * @param string      $propertyName
     * @param string      $embeddedColumnName
     * @param null|string $className
     * @param null|string $embeddedClassName
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null): string {
        return $propertyName.ucfirst($embeddedColumnName);
    }

    /**
     * Returns a join column name for a property in camelCase (e.g. `propertyNameId`).
     *
     * @param string     $propertyName a property name
     * @param null|mixed $className
     */
    public function joinColumnName($propertyName, $className = null): string {
        return $propertyName.ucfirst($this->referenceColumnName());
    }

    /**
     * Returns the foreign key column name for the given parameters in camelCase.
     *
     * @param string      $entityName           an entity
     * @param null|string $referencedColumnName a property
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null): string {
        return $this->classToCamelCase($entityName).
                ucfirst($referencedColumnName ?: $this->referenceColumnName());
    }

    /**
     * Returns unqualified class name (without namespace path)
     * (wihout altering case --> PascalCase).
     *
     * @param string $className
     */
    private function unqualifiedClassName($className): string {
        if (false !== strpos($className, '\\')) {
            return substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

    /**
     * Returns ClassName in snake_case.
     *
     * @param string $className
     */
    private function classToSnakeCase($className): string {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $this->unqualifiedClassName($className))), '_');
    }

    /**
     * Returns ClassName in camelCase.
     *
     * @param string $className
     */
    private function classToCamelCase($className): string {
        return lcfirst($this->unqualifiedClassName($className));
    }
}
