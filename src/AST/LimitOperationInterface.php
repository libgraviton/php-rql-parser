<?php

namespace Graviton\Rql\AST;

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
