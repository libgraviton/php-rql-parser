<?php

namespace Graviton\Rql;

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
        foreach ($expected AS $part => $type) {
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
                ')' => Lexer::T_CLOSE_PARENTHESIS
            )),
            'simple ne' => array('ne(name,foo)', array('ne' => Lexer::T_NE)),
        );
    }
}
