<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface ArrayOperationInterface
{
    /**
     * @param string $property
     *
     * @return void
     */
    public function setProperty($property);

    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param string $value possible value
     *
     * @return void
     */
    public function addValue($value);

    /**
     * @return string[]
     */
    public function getArray();
}
