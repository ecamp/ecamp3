<?php
/**
 * Created by PhpStorm.
 * User: pirminmattmann
 * Date: 17.04.14
 * Time: 11:08
 */

namespace EcampLib\Form;

class FilteredIterator implements \Iterator
{
    private $iterator;
    private $allowedKeys;

    public function __construct(\Iterator $iterator, $allowedKeys = array())
    {
        $this->iterator = $iterator;
        $this->allowedKeys = $allowedKeys;

        $this->gotoAllowedKey();
    }

    private function gotoAllowedKey()
    {
        while ($this->iterator->valid() && !in_array($this->iterator->current()->getName(), $this->allowedKeys)) {
            $this->iterator->next();
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->iterator->next();
        $this->gotoAllowedKey();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iterator->rewind();
        $this->gotoAllowedKey();
    }
}
