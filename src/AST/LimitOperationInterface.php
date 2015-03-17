<?php
/**
 * interface for "limit()" operations
 */

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
interface LimitOperationInterface
{
    /**
     * @param int $limit limit
     *
     * @return void
     */
    public function setLimit($limit);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $skip skip
     *
     * @return void
     */
    public function setSkip($skip);

    /**
     * @return int
     */
    public function getSkip();
}
