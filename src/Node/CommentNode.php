<?php
/**
 * node implementation for comment
 */

namespace Graviton\Rql\Node;

use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\RqlEncoder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class CommentNode extends AbstractQueryNode
{
    /**
     * Singleton search param
     */
    private static $instance;

    /** @var bool */
    private $visited = false;

    /**
     * @var string
     */
    protected $comment = '';

    /**
     * Constructor.
     * @param string $comment comment
     */
    public function __construct(string $comment = '')
    {
        $this->comment = $comment;
    }

    /**
     * Singleton implementation
     * @return CommentNode
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new CommentNode();
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
        return 'comment';
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
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
            RqlEncoder::encode($this->comment)
        );
    }
}
