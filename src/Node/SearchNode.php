<?php
/**
 * node implementation for search
 */

namespace Graviton\Rql\Node;

use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/GravitonRqlParserBundle/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchNode extends AbstractQueryNode
{
    /**
     * @var array
     */
    protected $searchTerms;

    /**
     * @param array $searchTerms What search parameters should be added on custurction
     */
    public function __construct(array $searchTerms = [])
    {
        $this->searchTerms = $searchTerms;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getNodeName()
    {
        return 'search';
    }

    /**
     * @param string $searchTerm Search term as a string
     * @return void
     */
    public function addSearchTerm($searchTerm)
    {
        $this->searchTerms[$searchTerm] = $searchTerm;
    }

    /**
     * @return array
     */
    public function getSearchTerms()
    {
        return $this->searchTerms;
    }
}
