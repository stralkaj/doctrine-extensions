<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 03.05.2017
 * Time: 23:03
 */

namespace OnlineImperium\DoctrineExtensions\DqlFunctions;


use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

class RoundFunction extends FunctionNode
{
    private $arithmeticExpression;

    public function getSql(SqlWalker $sqlWalker)
    {

        return 'ROUND(' . $sqlWalker->walkSimpleArithmeticExpression(
            $this->arithmeticExpression
        ) . ')';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {

        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}