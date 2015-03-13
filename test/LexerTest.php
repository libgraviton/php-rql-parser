<?php

namespace Graviton\Rql;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test lexer
     *
     * @dataProvider lexerProvider
     */
    public function testLexer($rql, $expected)
    {
        $sut = new \Graviton\Rql\Lexer;
        $sut->setInput($rql);
        foreach ($expected as $part => $type) {
            $sut->moveNext();
            $this->assertEquals($part, $sut->lookahead['value']);
            $this->assertEquals($type, $sut->lookahead['type'], sprintf('type mismatch for %s', $part));
        }
    }

    /**
     * @return array<string>
     */
    public function lexerProvider()
    {
        return array(
            'simple eq' => array('eq(name,foo bar)', array(
                'eq' => Lexer::T_EQ,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'name' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                'foo bar' => Lexer::T_STRING,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
            )),
            'simple ne' => array('ne(name,foo)', array('ne' => Lexer::T_NE)),
            'simple and' => array('and(eq(name,foo),ne(name,bar))', array(
                'and' => Lexer::T_AND,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'eq' => Lexer::T_EQ,
                '(' => Lexer::T_OPEN_PARENTHESIS,
            )),
            'integer' => array('eq(count,1)', array(
                'eq' => Lexer::T_EQ,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'count' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                '1' => Lexer::T_INTEGER
            )),
            'simple or' => array('or(eq(name,foo),eq(name,bar))', array(
                'or' => Lexer::T_OR,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'eq' => Lexer::T_EQ,
                '(' => Lexer::T_OPEN_PARENTHESIS,
            )),
            'lt,gt' => array('lt(),gt())', array(
                'lt' => Lexer::T_LT,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
                ',' => Lexer::T_COMMA,
                'gt' => Lexer::T_GT,
            )),
            'lte,gte' => array('lte(),gte())', array(
                'lte' => Lexer::T_LTE,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
                ',' => Lexer::T_COMMA,
                'gte' => Lexer::T_GTE,
            )),
            'sort' => array('sort(+count,-name)', array(
                'sort' => Lexer::T_SORT,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                '+' => Lexer::T_PLUS,
                'count' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                '-' => Lexer::T_MINUS,
                'name' => Lexer::T_STRING,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
            )),
            'like' => array('like(name,fo*)', array(
                'like' => Lexer::T_LIKE,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'name' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                'fo*' => Lexer::T_STRING,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
             )),
            'limit' => array('limit(1,2)', array(
                'limit' => Lexer::T_LIMIT,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                '1' => Lexer::T_INTEGER,
                ',' => Lexer::T_COMMA,
                '2' => Lexer::T_INTEGER,
                ')' => Lexer::T_CLOSE_PARENTHESIS,
            )),
            'in tests' => array('in(name,[foo,bar]', array(
                'in' => Lexer::T_IN,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'name' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                '[' => Lexer::T_OPEN_BRACKET,
                'foo' => Lexer::T_STRING
            )),
            'out tests' => array('out(name,[foo,bar]', array(
                'out' => Lexer::T_OUT,
                '(' => Lexer::T_OPEN_PARENTHESIS,
                'name' => Lexer::T_STRING,
                ',' => Lexer::T_COMMA,
                '[' => Lexer::T_OPEN_BRACKET,
                'foo' => Lexer::T_STRING
            )),
        );
    }
}
