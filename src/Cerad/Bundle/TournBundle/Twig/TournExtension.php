<?php
namespace Cerad\Bundle\TournBundle\Twig;

class TournExtension extends \Twig_Extension
{
    protected $env;
    protected $project;
    protected $showConfig;
    
    public function getName()
    {
        return 'cerad_tourn_extension';
    }
    public function __construct($project,$showConfigs)
    {
        $this->project = $project;
        
        $configName = defined('CERAD_TOURN_SHOW_CONFIG') ? CERAD_TOURN_SHOW_CONFIG : 'default';

        if (!isset($showConfigs[$configName]))
        {
            throw new \Exception('Undefined show config : ' . $configName);
        }
        $this->showConfig = $showConfigs[$configName];
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
            'cerad_tourn_show' => new \Twig_Function_Method($this, 'show'),
            
          //'cerad_tourn_is_iframe'   => new \Twig_Function_Method($this, 'isIFrame'),
            'cerad_tourn_get_referer' => new \Twig_Function_Method($this, 'getReferer'),
            
            'cerad_tourn_get_project_title'       => new \Twig_Function_Method($this, 'getProjectTitle'),
            'cerad_tourn_get_project_description' => new \Twig_Function_Method($this, 'getProjectDescription'),            
        );
    }
    public function getProjectDescription()
    {
        return $this->project->getDesc();
    }
    public function getProjectTitle()
    {
        return $this->project->getTitle();
    }
    public function getReferer()
    {
        // Should be a better way than to access $_SERVER directly.
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        if (!$url) return null;
        
        $parts = parse_url($url);
        
        $referer = sprintf('%s://%s/',$parts['scheme'],$parts['host']);
      //die($referer);
        return $referer;
        
    }
    public function show($param)
    {
        if (!isset($this->showConfig[$param]))
        {
            throw new \Exception('Undefined show config param : ' . $param);
        }
        return $this->showConfig[$param];
    }
}
?>
