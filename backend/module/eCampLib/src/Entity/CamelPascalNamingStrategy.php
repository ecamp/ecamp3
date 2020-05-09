<?php

namespace eCamp\Lib\Entity;

use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class CamelPascalNamingStrategy extends DefaultNamingStrategy {
    /**
     * Returns a table name for an entity class in snake_case
     * PascalCase is bad practice as many DB backend are not case sensitive.
     *
     * @param string $className the fully-qualified class name
     *
     * @return string a table name
     */
    public function classToTableName($className) {
        return $this->classToSnakeCase($className);
    }

    /**
     * Returns a column name for an embedded property in camelCase.
     *
     * @param string $propertyName
     * @param string $embeddedColumnName
     * @param string $className
     * @param string $embeddedClassName
     *
     * @return string
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null) {
        return $propertyName.ucfirst($embeddedColumnName);
    }

    /**
     * Returns a join column name for a property in camelCase (e.g. `propertyNameId`).
     *
     * @param string     $propertyName a property name
     * @param null|mixed $className
     *
     * @return string a join column name
     */
    public function joinColumnName($propertyName, $className = null) {
        return $propertyName.ucfirst($this->referenceColumnName());
    }

    /**
     * Returns the foreign key column name for the given parameters in camelCase.
     *
     * @param string      $entityName           an entity
     * @param null|string $referencedColumnName a property
     *
     * @return string a join column name
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null) {
        return $this->classToCamelCase($entityName).
                ucfirst(($referencedColumnName ?: $this->referenceColumnName()));
    }

    /**
     * Returns unqualified class name (without namespace path)
     * (wihout altering case --> PascalCase).
     *
     * @param mixed $className
     */
    private function unqualifiedClassName($className) {
        if (false !== strpos($className, '\\')) {
            return substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

    /**
     * Returns ClassName in snake_case.
     *
     * @param mixed $className
     */
    private function classToSnakeCase($className) {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $this->unqualifiedClassName($className))), '_');
    }

    /**
     * Returns ClassName in camelCase.
     *
     * @param mixed $className
     */
    private function classToCamelCase($className) {
        return lcfirst($this->unqualifiedClassName($className));
    }
}
