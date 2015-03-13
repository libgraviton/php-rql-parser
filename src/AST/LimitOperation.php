<?php

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

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setSkip($skip)
    {
        $this->skip = $skip;
    }

    public function getSkip()
    {
        return $this->skip;
    }
}
