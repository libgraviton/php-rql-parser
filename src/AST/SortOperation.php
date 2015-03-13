<?php

namespace Graviton\Rql\AST;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
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
