<?php
/**
 * "sort()"
 */

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

    /**
     * @param string $field field
     *
     * @return void
     */
    public function addField($field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
