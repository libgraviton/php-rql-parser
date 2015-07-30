<?php
/**
 * event for visiting single nodes
 */

namespace Graviton\Rql\Event;

use Symfony\Component\EventDispatcher\Event;
use Xiag\Rql\Parser\AbstractNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
final class VisitNodeEvent extends Event
{
    /**
     * @var AbstractNode
     */
    private $node;

    /**
     * @param AbstracNode $node any type of node we are visiting
     */
    public function __construct(AbstractNode $node)
    {
        $this->node = $node;
    }

    /**
     * @return AbstractNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param AbstractNode $node replacement node
     *
     * @return void
     */
    public function setNode(AbstractNode $node)
    {
        $this->node = $node;
    }
}
