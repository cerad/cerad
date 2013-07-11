<?php
namespace Cerad\Bundle\AccountBundle\Entity;

use Cerad\Bundle\AccountBundle\Functions\Guid;

class UserIdentifier
{    
    protected $id;      // Guid for now, contains addisitonal profile info
    
    protected $user;    // aka entiry
    protected $value;
    
    protected $name;    // aka user display name
    protected $source;  // aka provider name
    protected $profile;
   
    protected $status = 'Active';
    
    protected $createdOn;
    
    public function setName     ($value) { $this->name      = $value; }
    public function setUser     ($value) { $this->user      = $value; }
    public function setValue    ($value) { $this->value     = $value; }
    public function setSource   ($value) { $this->source    = $value; }
    public function setStatus   ($value) { $this->status    = $value; }
  //public function setAccount  ($value) { $this->account   = $value; }
    public function setProfile  ($value) { $this->profile   = $value; }
    public function setCreatedOn($value) { $this->createdOn = $value; }
    
    public function getName()      { return $this->name;      }
    public function getUser()      { return $this->user;      }
    public function getValue()     { return $this->value;     }
    public function getSource()    { return $this->source;    }
    public function getStatus()    { return $this->status;    }
  //public function getAccount()   { return $this->account;   }
    public function getProfile()   { return $this->profile;   }
    public function getCreatedOn() { return $this->createdOn; }
    
    public function __construct()
    {
        $this->id = Guid::gen();
        
        $this->createdOn = new \DateTime();
    }
    public function loadFromArray($item)
    {
        // Keep it simple for now
        $props = array
        (
            'id'      => 'id',
            'source'  => 'source',
            'value'   => 'value',
            'name'    => 'name',
            'status'  => 'status',
            'profile' => 'profile',
        );
        foreach($props as $key => $value)
        {
            $this->$key = $item[$value];
        }
        // Handle dates
        $props = array
        (
            'createdOn' => 'created_on',
        );
        foreach($props as $key => $value)
        {
            $date = $item[$value];
            if ($date)
            {
                $date = new \DateTime($date);
                $this->$key = $date;
            }
        }
        return $this;
     }     
}
?>