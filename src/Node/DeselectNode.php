<?php
/**
 * node implementation for deselect
 */

namespace Graviton\Rql\Node;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class DeselectNode extends AbstractQueryNode
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * SearchNode constructor.
     * @param array $searchTerms list of search items.
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getNodeName()
    {
        return 'deselect';
    }

    /**
     * get Fields
     *
     * @return array Fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * set Fields
     *
     * @param array $fields fields
     *
     * @return void
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }
}
