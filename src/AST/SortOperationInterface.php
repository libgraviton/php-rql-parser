<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface SortOperationInterface
{
    /**
     * @param string[]
     */
    public function addField($field);

    /**
     * @return array<string[]>
     */
    public function getFields();
}
