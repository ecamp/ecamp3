<?php
namespace EcampLib\Twig;

use Twig_Token;
use Twig_TokenParser;

class RenderViewModelTokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $viewModelName = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new RenderViewModelNode($viewModelName, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'RenderViewModel';
    }
}
