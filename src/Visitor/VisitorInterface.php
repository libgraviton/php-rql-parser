<?php

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;

/**
 * Interface VisitorInterface
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
interface VisitorInterface
{
    /**
     * Acts on the operation defined.
     *
     * @param OperationInterface $operation
     */
    public function visit(OperationInterface $operation);
}
