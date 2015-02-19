<?php

namespace Graviton\Rql\AST;

class SortOperation implements SortOperationInterface
{
        /**
         * @var
         */
        public $fields;

        public function getFields()
        {
            return $this->fields;
        }
}
