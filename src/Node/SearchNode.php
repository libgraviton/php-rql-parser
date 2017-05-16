<?php
/**
 * node implementation for search
 */

namespace Graviton\Rql\Node;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class SearchNode extends AbstractQueryNode
{
    /**
     * Singleton search param
     */
    private static $instance;

    /** @var bool */
    private $visited = false;

    /**
     * @var array
     */
    protected $searchTerms = [];

    /**
     * SearchNode constructor.
     * @param array $searchTerms list of search items.
     */
    public function __construct(array $searchTerms = [])
    {
        $this->searchTerms = $searchTerms;
    }

    /**
     * Singleton implementation
     * @return SearchNode
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new SearchNode();
        }

        return self::$instance;
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
        $string = trim($searchTerm);
        if ($string && !in_array($string, $this->searchTerms)) {
            $this->searchTerms[] = $string;
        }
    }

    /**
     * Elements to be searched for
     *
     * @return array
     */
    public function getSearchTerms()
    {
        return $this->searchTerms;
    }

    /**
     * if visited
     *
     * @return bool
     */
    public function isVisited()
    {
        return $this->visited;
    }

    /**
     * Set visited
     *
     * @param bool $visited Change visit status
     * @return void
     */
    public function setVisited($visited)
    {
        $this->visited = $visited;
    }

    /**
     * Enable TESTs so the singleton can be reset
     * @return void
     */
    public function resetSearchTerms()
    {
        $this->searchTerms = [];
        $this->visited = false;
    }
}
