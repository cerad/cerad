<?php
namespace Cerad\Bundle\AccountBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;

class AccountUser extends BaseUser
{
    protected $name;
    protected $person; // Just a guid for now
    
    public function getName()       { return $this->name; }
    public function setName($value) { $this->name = $value; return $this; }
    
    public function getPerson()       { return $this->person; }
    public function setPerson($value) { $this->person = $value; return $this; }
    
    public function setEmail($value)
    {
        $this->email = $value;
        if (!$this->username) $this->username = $value;
    }
}

?>
