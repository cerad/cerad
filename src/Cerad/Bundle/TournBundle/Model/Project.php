<?php
namespace Cerad\Bundle\TournBundle\Model;

/* ======================================================
 * Need a tournament specific project entity that links PersonBundle and the GameBundle
 * Maybe we need a ProjectBundle?
 * 
 * Tournaments focus on one and only one project
 * The project will typically contain plan meta data stored in a config file
 */
class Project
{
    protected $config;
    
    public function __construct($config)
    {
        $this->config = $config;
    }
    public function getKey()   { return $this->config['info']['key'];   }
    public function getTitle() { return $this->config['info']['title']; }
    public function getDesc()  { return $this->config['info']['desc'];  }
    public function getPlan()  { return $this->config['plan']; }
}
?>
