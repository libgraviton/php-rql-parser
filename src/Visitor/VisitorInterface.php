<?php

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface VisitorInterface
{
    public function visit(OperationInterface $operation);
}
