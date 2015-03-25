<?php
/**
 * RQL Visitor implementation
 */

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface VisitorInterface
{
    /**
     * Acts on the operation defined.
     *
     * @param OperationInterface $operation AST representation of query
     *
     * @return mixed
     */
    public function visit(OperationInterface $operation);
}
