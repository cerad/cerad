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
    public function getInfo()  { return $this->config['info']; }
    
    public function getKey()        { return $this->config['info']['key'];       }
    public function getTitle()      { return $this->config['info']['title'];     }
    public function getDesc()       { return $this->config['info']['desc'];      }
    public function getDomain()     { return $this->config['info']['domain'];    }
    public function getDomainSub()  { return $this->config['info']['domainSub']; }
    public function getSeason()     { return $this->config['info']['season'];    }
    public function getSport()      { return $this->config['info']['sport'];     }
    
    public function getPlan()       { return $this->config['plan']; }
    
    public function getDates()      { return isset($this->config['dates'])   ? $this->config['dates'] : array(); }
    public function getAges()       { return isset($this->config['ages'])    ? $this->config['ages'] : array(); }
    public function getGenders()    { return isset($this->config['genders']) ? $this->config['genders'] : array(); }
}
?>
