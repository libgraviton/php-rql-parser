<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
abstract class AbstractQueryOperation extends AbstractOperation implements QueryOperationInterface
{
    /**
     * @var OperationInterface[]
     */
    private $queries = array();

    /**
     * @param OperationInterface $query query to add
     *
     * @return void
     */
    public function addQuery($query)
    {
        $this->queries[] = $query;
    }

    /**
     * @return OperationInterface[]
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
