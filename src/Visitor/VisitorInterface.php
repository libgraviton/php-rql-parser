<?php
/**
 * RQL Visitor implementation
 */

namespace Graviton\Rql\Visitor;

use Xiag\Rql\Parser\Query;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
interface VisitorInterface
{
    /**
     * Acts on the operation defined.
     *
     * @param Query $query abstract representation of query
     *
     * @return mixed
     */
    public function visit(Query $query);
}
