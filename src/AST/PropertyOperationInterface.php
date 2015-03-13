<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface PropertyOperationInterface
{
    /**
     * @param string $property name
     */
    public function setProperty($property);

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param mixed $value value
     */
    public function setValue($value);

    /**
     * @return mixed
     */
    public function getValue();
}
