<?php
namespace Cerad\Bundle\PersonBundle\Twig;

class PersonTwigExtension extends \Twig_Extension
{
    protected $env;
    
    public function getName()
    {
        return 'cerad_person_extension';
    }
    public function __construct($phoneTransformer)
    {
        $this->phoneTransformer = $phoneTransformer;
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
    public function getFilters()
    {
        return array(            
            new \Twig_SimpleFilter('cerad_person_phone',array($this, 'transformPhone')),        
        );
    }
    public function transformPhone($phone)
    {
        return $this->phoneTransformer->transform($phone);
    }
}
?>
