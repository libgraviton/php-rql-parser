<?php
/**
 * interface for all AST operations
 */

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface OperationInterface
{
    /**
     * @param VisitorInterface $visitor visitor
     *
     * @return void
     */
    public function accept(VisitorInterface $visitor);
}
