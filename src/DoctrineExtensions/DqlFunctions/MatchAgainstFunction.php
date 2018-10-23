<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 04.05.2017
 * Time: 1:49
 */

namespace OnlineImperium\DoctrineExtensions\DqlFunctions;


use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Zdroje:
 * https://gist.github.com/ZeBigDuck/1234419/edaa13a851b1ea1e9926ec9c003ad762876ffe1d
 * http://stackoverflow.com/questions/17534796/match-against-script-is-not-working-with-symfony2
 * "MATCH_AGAINST" "(" {StateFieldPathExpression ","}* Literal ")"
 */
class MatchAgainstFunction extends FunctionNode {
    /** @var array list of \Doctrine\ORM\Query\AST\PathExpression */
    protected $pathExp = null;

    /** @var string */
    protected $against = null;

    /** @var boolean */
    protected $booleanMode = false;

    /** @var boolean */
    protected $queryExpansion = false;

    public function parse(Parser $parser) {
        // match
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        // first Path Expression is mandatory
        $this->pathExp = array();
        $this->pathExp[] = $parser->StateFieldPathExpression();

        // Subsequent Path Expressions are optional
        $lexer = $parser->getLexer();
        while ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->pathExp[] = $parser->StateFieldPathExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        // against
        if (strtolower($lexer->lookahead['value']) !== 'against') {
            $parser->syntaxError('against');
        }

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->against = $parser->StringPrimary();

        if (strtolower($lexer->lookahead['value']) === 'boolean') {
            $parser->match(Lexer::T_IDENTIFIER);
            $this->booleanMode = true;
        }

        if (strtolower($lexer->lookahead['value']) === 'expand') {
            $parser->match(Lexer::T_IDENTIFIER);
            $this->queryExpansion = true;
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $walker) {
        $fields = array();
        foreach ($this->pathExp as $pathExp) {
            $fields[] = $pathExp->dispatch($walker);
        }

        $against = $walker->walkStringPrimary($this->against)
            . ($this->booleanMode ? ' IN BOOLEAN MODE' : '')
            . ($this->queryExpansion ? ' WITH QUERY EXPANSION' : '');

        return sprintf('MATCH (%s) AGAINST (%s)', implode(', ', $fields), $against);
    }
}