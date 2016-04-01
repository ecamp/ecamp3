<?php
namespace EcampLib\Twig;

use Twig_Node;
use Twig_Compiler;

class RenderViewModelNode extends Twig_Node
{
    public function __construct($name, $line, $tag)
    {
        parent::__construct(array('name' => $name), array(), $line, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $viewModelName = $this->getNode('name');

        $compiler
            ->addDebugInfo($this)
            ->write("\$viewModel = ")
            ->subcompile($viewModelName)
            ->raw(";\n")
            ->write("if (\$viewModel instanceOf \\Zend\\View\\Model\\ModelInterface) {\n")
            ->indent()
                ->write("\$template = \$this->env->resolveTemplate(")
                ->raw("\$viewModel->getTemplate()")
                ->raw(");\n")
                ->write("\$template->display(")
                ->raw("\$viewModel->getVariables()->getArrayCopy()")
                ->raw(");\n")
            ->outdent()
            ->write("} else {\n")
            ->indent()
                ->write("echo '<b>ViewModel [" . $viewModelName->getAttribute('name') . "] not found.</b>';\n")
            ->outdent()
            ->write("}\n");
    }
}
