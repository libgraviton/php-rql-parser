<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
abstract class AbstractArrayOperation extends AbstractOperation implements ArrayOperationInterface
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string[]
     */
    private $array = array();

    /**
     * @var string $property property name
     *
     * @return void
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $value value operation
     *
     * @return void
     */
    public function addValue($value)
    {
        $this->array[] = $value;
    }

    /**
     * @return string[]
     */
    public function getArray()
    {
        return $this->array;
    }
}
