<?php
namespace Cerad\Bundle\TournBundle\Twig;

class TournExtension extends \Twig_Extension
{
    protected $env;
    
    public function getName()
    {
        return 'cerad_schedule_extension';
    }
    
    public function initRuntime(\Twig_Environment $env)
    {
        parent::initRuntime($env);
        $this->env = $env;
    }
    protected function escape($string)
    {
        return twig_escape_filter($this->env,$string);
    }
    public function getFunctions()
    {
        return array(            
            'cerad_tourn_show_header'   => new \Twig_Function_Method($this, 'showHeader'),
        );
    }
    public function showHeader()
    {
        if (defined('CERAD_TOURN_SHOW_HEADER')) return CERAD_TOURN_SHOW_HEADER;
        return true;
    }
}
?>
