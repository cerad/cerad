<?php
namespace Cerad\Bundle\AccountBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;

use Doctrine\Common\Collections\ArrayCollection;

class AccountUser extends BaseUser
{
    protected $name;
    protected $person;      // Just a guid for now
    protected $personGuid;  // Just a guid for now
    protected $identifiers; // Allow cascade persisting
    
    public function getName()       { return $this->name; }
    public function setName($value) { $this->name = $value; return $this; }
    
    public function getPersonGuid()       { return $this->personGuid; }
    public function setPersonGuid($value) { $this->personGuid = $value; return $this; }
    
    public function getPerson()       { return $this->person; }
    public function setPerson($value) { $this->person = $value; return $this; }
    
    public function setEmail($value)
    {
        $this->email = $value;
        if (!$this->username) $this->username = $value;
    }
    public function addIdentifier($identifier)
    {
        // Filter duplicates
        $this->identifiers[] = $identifier;
        $identifier->setAccount($this);
    }
    public function getIdentifiers() { return $this->identifiers; }
    
    /* ======================================================
     * This is a bit hokaay but in general one could expect that the
     * user knows something about it's identifiers
     * 
     * The manage might be a better place for this
     */
    public function createIdentifier($providerName,$identifier,$profile = null)
    {
        return AccountIdentifier::create($providerName,$identifier,$profile);
    }
    public function __construct()
    {
        parent::__construct();
        $this->identifiers = new ArrayCollection();
    }
}

?>
