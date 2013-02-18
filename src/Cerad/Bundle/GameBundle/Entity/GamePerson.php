<?php
namespace Cerad\Bundle\GameBundle\Entity;

/* ========================================================
 * Probably habe too much person info here but wanted
 * something usable that did not require an actual link to a person object
 */
class GamePerson extends BaseEntity
{
    protected $id;
    
    protected $game;
    protected $person;  // Link to an unique person, probable use guid
    
    protected $slot; // 1-5 for arbiter
    protected $role; // Referee, AR1 etc
    
    protected $name;
    protected $email; // For arbiter, this serves as a unique identifier
    protected $phone;
    protected $badge;
    protected $league;
    
    protected $status; // Created, published, notified, accepted
    
    protected $fee;
    protected $workflow;  // Getting paid
    
    public function getId    () { return $this->id;     }
    public function getGame  () { return $this->game;   }
    public function getSlot  () { return $this->slot;   }
    public function getRole  () { return $this->role;   }
    public function getName  () { return $this->name;   }
    public function getEmail () { return $this->email;  }
    public function getPhone () { return $this->phone;  }
    public function getBadge () { return $this->badge;  }
    public function getLeague() { return $this->league; }
    public function getStatus() { return $this->status; }

    public function setGame  ($value) { $this->onPropertySet('game',  $value); }
    public function setSlot  ($value) { $this->onPropertySet('slot',  $value); } 
    public function setRole  ($value) { $this->onPropertySet('role',  $value); }
    public function setName  ($value) { $this->onPropertySet('name',  $value); }
    public function setEmail ($value) { $this->onPropertySet('email', $value); }
    public function setPhone ($value) { $this->onPropertySet('phone', $value); }
    public function setBadge ($value) { $this->onPropertySet('badge', $value); }
    public function setLeague($value) { $this->onPropertySet('league',$value); }
    public function setStatus($value) { $this->onPropertySet('status',$value); }
    
    static public function create($slot, $role, $name = null, $status = 'Created')
    {
        $person = new self();
        
        $person->setSlot  ($slot);
        $person->setRole  ($role);
        $person->setName  ($name);
        $person->setStatus($status);
        
        return $person;
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("GamePerson %d %-8s %s",
            $this->slot,
            $this->role,
            $this->name
        );
    }
     
}

?>
