<?php
/**
 * Parser class file
 */

namespace Graviton\Rql;

use Graviton\Rql\NodeParser\DeselectNodeParser;
use Graviton\Rql\NodeParser\ElemMatchNodeParser;
use Graviton\Rql\NodeParser\SearchNodeParser;
use Graviton\Rql\TokenParser\ElemMatchTokenParser;
use Graviton\Rql\TokenParser\SearchTokenParser;
use Xiag\Rql\Parser\NodeParser\LimitNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\EqNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\GeNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\GtNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\InNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\LeNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\LikeNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\LtNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\NeNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Rql\OutNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\GroupNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\LogicalOperator\AndNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\LogicalOperator\NotNodeParser;
use Xiag\Rql\Parser\NodeParser\Query\LogicalOperator\OrNodeParser;
use Xiag\Rql\Parser\NodeParser\QueryNodeParser;
use Xiag\Rql\Parser\NodeParser\SelectNodeParser;
use Xiag\Rql\Parser\NodeParser\SortNodeParser;
use Xiag\Rql\Parser\NodeParserChain;
use Xiag\Rql\Parser\Parser as BaseParser;
use Xiag\Rql\Parser\TokenParserInterface;
use Xiag\Rql\Parser\TypeCaster;
use Xiag\Rql\Parser\ExpressionParser;
use Xiag\Rql\Parser\TokenParserGroup;
use Xiag\Rql\Parser\TokenParser;
use Xiag\Rql\Parser\TypeCaster\BooleanTypeCaster;
use Xiag\Rql\Parser\TypeCaster\FloatTypeCaster;
use Xiag\Rql\Parser\TypeCaster\IntegerTypeCaster;
use Xiag\Rql\Parser\TypeCaster\StringTypeCaster;
use Xiag\Rql\Parser\ValueParser\ArrayParser;
use Xiag\Rql\Parser\ValueParser\FieldParser;
use Xiag\Rql\Parser\ValueParser\GlobParser;
use Xiag\Rql\Parser\ValueParser\IntegerParser;
use Xiag\Rql\Parser\ValueParser\ScalarParser;

/**
 * RQL parser
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class Parser extends BaseParser
{

    public static function createDefaultNodeParser()
    {
        $nodeParser = parent::createDefaultNodeParser();

        $scalarParser = (new ScalarParser())
            ->registerTypeCaster('string', new StringTypeCaster())
            ->registerTypeCaster('integer', new IntegerTypeCaster())
            ->registerTypeCaster('float', new FloatTypeCaster())
            ->registerTypeCaster('boolean', new BooleanTypeCaster());
        $arrayParser = new ArrayParser($scalarParser);
        $globParser = new GlobParser();
        $fieldParser = new FieldParser();
        $integerParser = new IntegerParser();

        $queryNodeParser = new QueryNodeParser();
        $queryNodeParser
            ->addNodeParser(new GroupNodeParser($queryNodeParser))

            ->addNodeParser(new AndNodeParser($queryNodeParser))
            ->addNodeParser(new OrNodeParser($queryNodeParser))
            ->addNodeParser(new NotNodeParser($queryNodeParser))

            // RQL
            ->addNodeParser(new InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new LikeNodeParser($fieldParser, $globParser))

            // our own stuff --> only rql!
            ->addNodeParser(new SearchNodeParser($scalarParser))
            ->addNodeParser(new DeselectNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ElemMatchNodeParser($queryNodeParser))

            // FIQL
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser));

        return (new NodeParserChain())
            ->addNodeParser($queryNodeParser)
            ->addNodeParser(new SelectNodeParser($fieldParser))
            ->addNodeParser(new SortNodeParser($fieldParser))
            ->addNodeParser(new LimitNodeParser($integerParser));
    }

}
