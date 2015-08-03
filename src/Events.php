<?php
/**
 * event for visiting single nodes
 */

namespace Graviton\Rql;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
final class Events
{
    /**
     * the rql.visit.node event is thrown each time an individual query node is being looked at
     *
     * @var string
     */
    const VISIT_NODE = 'rql.visit.node';
}
