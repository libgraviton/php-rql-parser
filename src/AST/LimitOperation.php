<?php

namespace Graviton\Rql\AST;

class LimitOperation implements LimitOperationInterface
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
