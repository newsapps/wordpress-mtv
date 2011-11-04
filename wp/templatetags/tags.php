<?php
namespace mtv\tags;

# Allows use of:
# {% while condition %} 
#   do stuff
# {% endwhile %}
class Twig_TokenParser_While extends \Twig_TokenParser {
    public function parse(\Twig_Token $token) {
        $lineno = $token->getLine();
        $expr = $this->parser->getExpressionParser()->parseExpression();
        
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideIfEnd'));
        $tests = array($expr, $body);
        $else = null;

        $end = false;
        $next_val = $this->parser->getStream()->next()->getValue();
        while (!$end) {
            switch ($next_val) {
                case 'endwhile':
                    $end = true;
                    break;
                default:
                    throw new \Twig_Error_Syntax(sprintf('Unexpected end of template. Twig was looking for "endwhile" to close the "while" block started at line %d)', $lineno), -1);
            }
        }

        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new Twig_Node_While(new \Twig_Node($tests), $else, $lineno, $this->getTag());
    }

    public function decideIfEnd(\Twig_Token $token) {
        return $token->test(array('endwhile'));
    }

    public function getTag() {
        return 'while';
    }
}
$twig->addTokenParser(new Twig_TokenParser_While());

# Compiles the actual php 
# for the {% while %} loop
class Twig_Node_While extends \Twig_Node {
    public function __construct(\Twig_NodeInterface $tests, $lineno, $tag = null) {
        parent::__construct(array('tests' => $tests), array(), $lineno, $tag);
    }

    public function compile(\Twig_Compiler $compiler) {
        $compiler->addDebugInfo($this);
        $compiler
            ->write('while (')
        ;

        for ($i = 0; $i < count($this->getNode('tests')); $i += 2) {
            $compiler
                ->subcompile($this->getNode('tests')->getNode($i))
                ->raw(") {\n")
                ->indent()
                ->subcompile($this->getNode('tests')->getNode($i + 1))
            ;
        }

        $compiler
            ->outdent()
            ->write("}\n");
    }
}
