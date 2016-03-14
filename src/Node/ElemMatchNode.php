<?php
/**
 * ElemMatchNode class file
 */

namespace Graviton\Rql\Node;

use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractComparisonOperatorNode;

/**
 * elemMatch() node
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class ElemMatchNode extends AbstractComparisonOperatorNode
{
    /**
     * @var AbstractQueryNode
     */
    protected $query;

    /**
     * Constructor
     *
     * @param string            $field Field
     * @param AbstractQueryNode $query Query
     */
    public function __construct($field, AbstractQueryNode $query)
    {
        $this->field = $field;
        $this->query = $query;
    }

    /**
     * Get node name
     *
     * @return string
     */
    public function getNodeName()
    {
        return 'elemMatch';
    }

    /**
     * Get query
     *
     * @return AbstractQueryNode
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set query
     *
     * @param AbstractQueryNode $query Query
     * @return void
     */
    public function setQuery(AbstractQueryNode $query)
    {
        $this->query = $query;
    }
}
