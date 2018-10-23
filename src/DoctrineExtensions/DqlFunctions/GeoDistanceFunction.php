<?php
/**
 * Created by PhpStorm.
 * User: Honza
 * Date: 04.05.2017
 * Time: 0:02
 */

namespace OnlineImperium\DoctrineExtensions\DqlFunctions;


use App\Model\GeoTools;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class GeoDistanceFunction extends FunctionNode
{
    private $lat1;
    private $lng1;
    private $lat2;
    private $lng2;
    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $lat1 = $sqlWalker->walkSimpleArithmeticExpression($this->lat1);
        $lat2 = $sqlWalker->walkSimpleArithmeticExpression($this->lat2);
        $lng1 = $sqlWalker->walkSimpleArithmeticExpression($this->lng1);
        $lng2 = $sqlWalker->walkSimpleArithmeticExpression($this->lng2);
        $kmPerLat2 = GeoTools::KM_PER_LATITUDE2;
        $kmPerLng2 = GeoTools::KM_PER_LONGITUDE2;
        // sqrt(k1*(lat1-lat2)*(lat1-lat2) + k2*(lng1-lng2)*(lng1-lng2)
        return "(SQRT($kmPerLat2 * ($lat1 - $lat2) * ($lat1 - $lat2) + $kmPerLng2 * ($lng1 - $lng2) * ($lng1 - $lng2)))";
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     *
     * @return void
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->lat1 = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);

        $this->lng1 = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);

        $this->lat2 = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);

        $this->lng2 = $parser->SimpleArithmeticExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}