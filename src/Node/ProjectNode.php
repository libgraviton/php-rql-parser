<?php
/**
 * node implementation for search
 */

namespace Graviton\Rql\Node;

use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class ProjectNode extends AbstractQueryNode
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
    protected array $projections = [];

    public function __construct(array $projections = [])
    {
        $this->projections = $projections;
    }

    /**
     * Singleton implementation
     * @return SearchNode
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ProjectNode();
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
        return 'project';
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
     * turns this node to rql..
     *
     * @return string rql
     */
    public function toRql()
    {
        return sprintf(
            '%s(%s)',
            $this->getNodeName(),
            RqlEncoder::encodeList($this->projections)
        );
    }
}
