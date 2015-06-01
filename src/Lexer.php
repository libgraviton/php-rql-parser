<?php
/**
 * lex rql queries using doctrine/lexer
 */

namespace Graviton\Rql;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link    http://swisscom.ch
 */
class Lexer extends \Doctrine\Common\Lexer
{
    const T_NONE              = 1;
    const T_INTEGER           = 2;
    const T_STRING            = 3;
    const T_OPEN_BRACKET      = 4;
    const T_CLOSE_BRACKET     = 5;
    const T_CLOSE_PARENTHESIS = 6;
    const T_OPEN_PARENTHESIS  = 7;
    const T_COMMA             = 8;
    const T_DOT               = 9;
    const T_SLASH             = 10;
    const T_SINGLE_QUOTE      = 11;
    const T_DOUBLE_QUOTE      = 12;

    const T_EQ    = 100;
    const T_NE    = 101;
    const T_AND   = 102;
    const T_OR    = 103;
    const T_LT    = 104;
    const T_GT    = 105;
    const T_LTE   = 106;
    const T_GTE   = 107;
    const T_SORT  = 108;
    const T_PLUS  = 109;
    const T_MINUS = 110;
    const T_LIKE  = 111;
    const T_LIMIT = 112;
    const T_IN    = 113;
    const T_OUT   = 114;
    const T_COLON = 115;

    /**
     * @var array<string>
     */
    private $primitiveMap = array(
        ',' => self::T_COMMA,
        '[' => self::T_OPEN_BRACKET,
        ']' => self::T_CLOSE_BRACKET,
        '(' => self::T_OPEN_PARENTHESIS,
        ')' => self::T_CLOSE_PARENTHESIS,
        '+' => self::T_PLUS,
        '-' => self::T_MINUS,
        '.' => self::T_DOT,
        '/' => self::T_SLASH,
        ':' => self::T_COLON,
    );

    /**
     * @var array<string>
     */
    protected static $fieldConcatenatorMap = array(
        '+' => self::T_PLUS,
        '-' => self::T_MINUS,
        '.' => self::T_DOT,
        '/' => self::T_SLASH,
        ':' => self::T_COLON,
    );

    /**
     * @var array<string>
     */
    protected static $fieldQuotesMap = array(
        "'" => self::T_SINGLE_QUOTE,
        '"' => self::T_DOUBLE_QUOTE,
    );

    /**
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            '\(',
            '\)',
            '\[',
            '\]',
            '[\w\s\*]+',
        );
    }

    /**
     * @return array
     */
    protected function getOperators()
    {
        return array(
            'eq',
            'ne',
            'and',
            'or',
            'lt',
            'gt',
            'lte',
            'gte',
            'sort',
            'like',
            'limit',
            'in',
            'out',
        );
    }

    /**
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array();
    }

    /**
     * @param string $value value from lexer
     *
     * @return int
     */
    protected function getType(&$value)
    {
        if (is_numeric($value)) {
            $type = $this->getNumericType($value);

        } elseif (in_array($value, $this->getOperators())) {
            $type = $this->getConstantType($value);

        } elseif (in_array($value, array_keys($this->primitiveMap))) {
            $type = $this->primitiveMap[$value];

        } elseif (in_array($value, array_keys(static::$fieldQuotesMap))) {
            $type = static::$fieldQuotesMap[$value];

        } else {
            $type = self::T_STRING;
        }

        return $type;
    }

    /**
     * @param string $value potential float value
     *
     * @return int
     */
    protected function getNumericType($value)
    {
        $type = self::T_INTEGER;
        if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
            $type = self::T_FLOAT;
        }
        return $type;
    }

    /**
     * @param string $value type name
     *
     * @return int
     */
    protected function getConstantType($value)
    {
        $type = self::T_NONE;
        $constName = sprintf('self::T_%s', strtoupper($value));
        if (defined($constName)) {
            $type = constant($constName);
        }
        return $type;
    }

    /**
     * Determines that the provided type identifier is part of the field concatenator list.
     *
     * @param string $typeId Identifier of the character type
     *
     * @return bool
     */
    public static function isFieldConcatenationChar($typeId)
    {
        return in_array($typeId, static::$fieldConcatenatorMap);
    }

    /**
     * Determines that the provided type identifier is part of the field concatenator list.
     *
     * @param string $typeId Identifier of the character type
     *
     * @return bool
     */
    public static function isFieldQuotationChar($typeId)
    {
        return in_array($typeId, static::$fieldQuotesMap);
    }

    /**
     * @param string $quotationChar Quoting character, like " (double quote)
     *
     * @return bool
     */
    public static function isOpeningQuotation($quotationChar)
    {
        static $quotes = array();

        if (empty($quotes[$quotationChar])) {
            $quotes[$quotationChar] = 0;
        }

        ++$quotes[$quotationChar];

        // true : it's the first appearance of the quote
        // false  : it's the closing quote
        return 1 === $quotes[$quotationChar]%2;
    }
}
