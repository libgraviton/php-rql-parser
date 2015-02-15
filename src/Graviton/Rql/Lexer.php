<?php

namespace Graviton\Rql;

class Lexer extends \Doctrine\Common\Lexer
{
    const T_NONE              = 1;
    const T_INTEGER           = 2;
    const T_STRING            = 3;

    const T_CLOSE_PARENTHESIS = 6;
    const T_OPEN_PARENTHESIS  = 7;
    const T_COMMA             = 8;


    const T_EQ  = 100;
    const T_NE  = 101;
    const T_AND = 102;
    const T_OR  = 103;

    protected function getCatchablePatterns()
    {
        return array(
            '\(',
            '\)',
            '[\w\s]+',
        );
    }

    protected function getOperators()
    {
        return array(
            'eq',
            'ne',
            'and',
            'or'
        );
    }

    protected function getNonCatchablePatterns()
    {
        return array();
    }

    protected function getType(&$value)
    {
        $type = self::T_NONE;

        if (is_numeric($value)) {
            $type = self::T_INTEGER;
            if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                $type = self::T_FLOAT;
            }
        } elseif (in_array($value, $this->getOperators())) {
            $constName = sprintf('self::T_%s', strtoupper($value));
            if (defined($constName)) {
                $type = constant($constName);
            }
        } else {
            switch ($value) {
                case ',': $type = self::T_COMMA; break;
                case '(': $type = self::T_OPEN_PARENTHESIS; break;
                case ')': $type = self::T_CLOSE_PARENTHESIS; break;
                default: $type = self::T_STRING; break;
            }
        }

        return $type;
    }
}
