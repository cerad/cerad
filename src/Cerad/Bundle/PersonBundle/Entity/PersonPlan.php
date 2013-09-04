<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* =======================================
 * Refactored to make the project key the actual project id
 * 
 * plan.plan
 */
class PersonPlan extends BaseEntity
{
    protected $id;
    protected $person;
    protected $projectId;
    protected $status   = 'Active';
    protected $verified = 'No';
    
    // These are basically value objects
    protected $basic = array();
    protected $avail;
    protected $level;
    protected $notes;
   
    public function __construct($id = null, $planProps = array())
    {
    //    $this->id = $id;
    //    $this->setPlanProperties($planProps);
    }
    public function getId()        { return $this->id;        }
    public function getPlan()      { return $this->basic;     }
    public function getBasic()     { return $this->basic;     }
    public function getPerson()    { return $this->person;    }
    public function getStatus()    { return $this->status;    }
    public function getVerified()  { return $this->verified;  }
    public function getProjectId() { return $this->projectId; }
    
    public function setId       ($value) { $this->id        = $value; }
    public function setPlan     ($value) { $this->basic     = $value; }
    public function setBasic    ($value) { $this->basic     = $value; }
    public function setPerson   ($value) { $this->person    = $value; }
    public function setStatus   ($value) { $this->status    = $value; }
    public function setVerified ($value) { $this->verified  = $value; }
    public function setProjectId($value) { $this->projectId = $value; }
    
    // TODO: Make this an array_merge
    public function setPlanProperties($props)
    {
        $plan = $this->basic; 
        foreach($props as $name => $prop)
        {
            $default = array_key_exists('default',$prop) ? $prop['default'] : null;
          
            if (!isset($plan[$name])) $plan[$name] = $default;
        }
        $this->basic = $plan;
    }
    public function __isset($name)
    {
        return array_key_exists($name,$this->basic);
        
        // Difference is that isset fails on null
        return isset($this->basic[$name]);
    }
    public function __get($name)
    {
        if (array_key_exists($name,$this->basic)) return $this->basic[$name];
    }
    /* =========================================
     * Maybe should trigger the notify routine?
     */
    public function __set($name,$value)
    {
        if (array_key_exists($name,$this->basic)) 
        {
            $this->basic[$name] = $value;
        }
    }
}
?>
