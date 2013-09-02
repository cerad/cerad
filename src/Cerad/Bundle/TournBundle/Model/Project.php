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
    
    public function getId()         { return $this->config['info']['key'];       }
    public function getKey()        { return $this->config['info']['key'];       }
    public function getTitle()      { return $this->config['info']['title'];     }
    public function getDesc()       { return $this->config['info']['desc'];      }
    public function getDomain()     { return $this->config['info']['domain'];    }
    public function getDomainSub()  { return $this->config['info']['domainSub']; }
    public function getSeason()     { return $this->config['info']['season'];    }
    public function getSport()      { return $this->config['info']['sport'];     }
    
    public function getPlan()       { return $this->config['plan']; }
    
    // Needs to be refined a bit
    public function getDates()      { return isset($this->config['searches']['dates'])   ? $this->config['searches']['dates']   : array(); }
    public function getAges()       { return isset($this->config['searches']['ages'])    ? $this->config['searches']['ages']    : array(); }
    public function getGenders()    { return isset($this->config['searches']['genders']) ? $this->config['searches']['genders'] : array(); }
    
    public function getSearches()   { return isset($this->config['searches']) ? $this->config['searches'] : array(); }
    
    // Bit of a hack for testing
    public function getKeySearch()  
    { 
        return isset(
            $this->config['info']['keySearch']) ? 
            $this->config['info']['keySearch']  : 
            $this->config['info']['key'];
    }

}
?>
