<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
abstract class AbstractOperation implements OperationInterface
{
    public function accept(VisitorInterface $visitor)
    {
        $visitor->visit($this);
    }
}
