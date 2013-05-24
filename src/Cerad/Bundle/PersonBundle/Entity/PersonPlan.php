<?php
namespace Cerad\Bundle\PersonBundle\Entity;

class PersonPlan extends BaseEntity
{
    protected $id;
    protected $person;
    protected $projectKey;        // The key for now
    protected $plan;              // Array for now
    protected $status   = 'Open'; // Just because
   
    public function __construct()
    {
        $this->plan = array();
    }
    public function getPlan()       { return $this->plan; }
    public function getPerson()     { return $this->person; }
    public function getStatus()     { return $this->status; }
    public function getProjectKey() { return $this->projectKey; }
    
    public function setPerson($value) { $this->person = $value; }
    public function setStatus($value) { $this->status = $value; }
    public function setPlan  ($value) { $this->plan   = $value; }
    
    public function setProjectKey($value) { $this->projectKey = $value; }
   
    public function setPlanProperties($props)
    {
        $plan = $this->plan;
        foreach($props as $name => $prop)
        {
            if (isset($prop['default'])) $default = $prop['default'];
            else                         $default = null;
          
            $plan[$name] = $default;
        }
        $this->plan = $plan;
    }
    public function __isset($name)
    {
        return isset($this->plan[$name]);
    }
    public function __get($name)
    {
        if (isset($this->plan[$name])) return $this->plan[$name];
    }
    /* =========================================
     * Maybe should trigger the notify routine?
     */
    public function __set($name,$value)
    {
        if (isset($this->plan[$name])) 
        {
            $this->plan[$name] = $value;
        }
    }
}
?>
