<?php
/**
 * simple listener for testing purposes
 */

namespace Graviton\Rql\Listener;

use Graviton\Rql\Event\VisitNodeEvent;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class TestListener
{
    /**
     * @param VisitNodeEvent $event node event to visit
     *
     * @return VisitNodeEvent
     */
    public function onVisitNode(VisitNodeEvent $event)
    {
        $node = $event->getNode();
        if ($node instanceof EqNode && $node->getValue() == 'replaceme') {
            $node->setValue('My First Sprocket');
            $event->setNode($node);
        }
        return $event;
    }
}
