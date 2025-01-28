<?php
/**
 * Parser class file
 */

namespace Graviton\Rql;

use Graviton\Rql\NodeParser\CommentNodeParser;
use Graviton\Rql\NodeParser\ElemMatchNodeParser;
use Graviton\Rql\NodeParser\SearchNodeParser;
use Graviton\RqlParser\NodeParser\DeselectNodeParser;
use Graviton\RqlParser\NodeParser\LimitNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\EqNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\GeNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\GtNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\InNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\LeNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\LikeNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\LtNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\NeNodeParser;
use Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Rql\OutNodeParser;
use Graviton\RqlParser\NodeParser\Query\GroupNodeParser;
use Graviton\RqlParser\NodeParser\Query\LogicalOperator\AndNodeParser;
use Graviton\RqlParser\NodeParser\Query\LogicalOperator\NotNodeParser;
use Graviton\RqlParser\NodeParser\Query\LogicalOperator\OrNodeParser;
use Graviton\RqlParser\NodeParser\QueryNodeParser;
use Graviton\RqlParser\NodeParser\SelectNodeParser;
use Graviton\RqlParser\NodeParser\SortNodeParser;
use Graviton\RqlParser\NodeParserChain;
use Graviton\RqlParser\Parser as BaseParser;
use Graviton\RqlParser\TypeCaster\BooleanTypeCaster;
use Graviton\RqlParser\TypeCaster\FloatTypeCaster;
use Graviton\RqlParser\TypeCaster\IntegerTypeCaster;
use Graviton\RqlParser\TypeCaster\StringTypeCaster;
use Graviton\RqlParser\ValueParser\ArrayParser;
use Graviton\RqlParser\ValueParser\FieldParser;
use Graviton\RqlParser\ValueParser\GlobParser;
use Graviton\RqlParser\ValueParser\IntegerParser;
use Graviton\RqlParser\ValueParser\ScalarParser;

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
            ->addNodeParser(new CommentNodeParser($scalarParser))
            ->addNodeParser(new ElemMatchNodeParser($queryNodeParser))

            // FIQL
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new \Graviton\RqlParser\NodeParser\Query\ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser));

        return (new NodeParserChain())
            ->addNodeParser($queryNodeParser)
            ->addNodeParser(new SelectNodeParser($scalarParser))
            ->addNodeParser(new DeselectNodeParser($scalarParser))
            ->addNodeParser(new SortNodeParser($fieldParser))
            ->addNodeParser(new LimitNodeParser($integerParser));
    }

}
