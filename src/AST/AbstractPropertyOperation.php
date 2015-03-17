<?php
/**
 * shared code for proerty type operators like "eq()" and "gt()"
 */

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
abstract class AbstractPropertyOperation extends AbstractOperation implements PropertyOperationInterface
{
    /**
     * @var string
     */
    private $property = '';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $property name
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
     * @param mixed $value value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
