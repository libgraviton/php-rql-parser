<?php

namespace Graviton\Rql\AST;

class LimitOperation implements LimitOperationInterface
{
        /**
         * @int
         */
        public $limit;

        /**
         * @int
         */
        public $skip;

        public function getLimit()
        {
            return $this->limit;
        }

        public function getSkip()
        {
            return $this->skip;
        }
}
