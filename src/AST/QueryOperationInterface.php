<?php
/**
 * interface for all query type operations like "and()" and "or()"
 */

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface QueryOperationInterface
{
    /**
     * @param OperationInterface $query query to add
     *
     * @return void
     */
    public function addQuery($query);

    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
