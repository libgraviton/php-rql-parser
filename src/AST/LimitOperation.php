<?php
/**
 * "limit()"
 */

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class LimitOperation extends AbstractOperation implements LimitOperationInterface
{
    /**
     * @int
     */
    private $limit;

    /**
     * @int
     */
    private $skip;

    /**
     * @param int $limit limit
     *
     * @return void
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $skip skip
     *
     * @return void
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;
    }

    /**
     * @return int
     */
    public function getSkip()
    {
        return $this->skip;
    }
}
