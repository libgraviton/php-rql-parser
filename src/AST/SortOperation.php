<?php

namespace Graviton\Rql\AST;

class SortOperation extends AbstractOperation implements SortOperationInterface
{
    /**
     * @var
     */
    private $fields = array();

    public function addField($field)
    {
        $this->fields[] = $field;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
