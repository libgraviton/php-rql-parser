<?php
/**
 * Parser class file
 */

namespace Graviton\Rql;

use Graviton\Rql\TokenParser\ElemMatchTokenParser;
use Graviton\Rql\TokenParser\SearchTokenParser;
use Xiag\Rql\Parser\Parser as BaseParser;
use Xiag\Rql\Parser\TokenParserInterface;
use Xiag\Rql\Parser\TypeCaster;
use Xiag\Rql\Parser\ExpressionParser;
use Xiag\Rql\Parser\TokenParserGroup;
use Xiag\Rql\Parser\TokenParser;

/**
 * RQL parser
 *
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link    http://swisscom.ch
 */
class Parser extends BaseParser
{
    /**
     * Create default parser
     *
     * @return Parser
     */
    public static function createDefault()
    {
        return (new static(
            (new ExpressionParser())
                ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
                ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
                ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
                ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster())
        ))
            ->addTokenParser(new TokenParser\SelectTokenParser())
            ->addTokenParser(static::createQueryTokenParser())
            ->addTokenParser(new TokenParser\SortTokenParser())
            ->addTokenParser(new TokenParser\LimitTokenParser())
            ->addTokenParser(new SearchTokenParser());
    }

    /**
     * Create query token parser
     *
     * @return TokenParserInterface
     */
    protected static function createQueryTokenParser()
    {
        $queryTokenParser = new TokenParserGroup();
        return $queryTokenParser
            ->addTokenParser(new TokenParser\Query\GroupTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\AndTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\OrTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\LogicOperator\NotTokenParser($queryTokenParser))
            ->addTokenParser(new TokenParser\Query\Basic\ArrayOperator\InTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ArrayOperator\OutTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\GeTokenParser())
            ->addTokenParser(new TokenParser\Query\Basic\ScalarOperator\LikeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ArrayOperator\InTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ArrayOperator\OutTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\EqTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\NeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LtTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\GtTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\GeTokenParser())
            ->addTokenParser(new TokenParser\Query\Fiql\ScalarOperator\LikeTokenParser())
            ->addTokenParser(new ElemMatchTokenParser($queryTokenParser));
    }
}
