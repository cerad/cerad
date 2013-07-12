<?php
namespace Cerad\Bundle\AccountBundle\Entity;

use FOS\UserBundle\Model\User as UserBase;

use Doctrine\Common\Collections\ArrayCollection;

use Cerad\Bundle\AccountBundle\Functions\Guid;

class User extends UserBase
{
    protected $name;
    protected $person;      // Linked Object, can be null, no autocreate
    protected $personId;    // Guid of any linked person
    protected $personNo;    // Flag to indicate that a persone will not be attached
    protected $createdOn;   // Nice for tracking
    
    protected $identifiers; // Allow cascade persisting
    
    public function setId($id) { $this->id = $id; return $this; }
    
    public function getName()       { return $this->name; }
    public function setName($value) { $this->name = $value; return $this; }
    
    public function getPerson  ()      { return $this->person;   }
    public function getPersonId()      { return $this->personId; }
    public function getPersonNo()      { return $this->personNo; }
    
    public function setPerson  ($item) { $this->person   = $item; return $this; }
    public function setPersonId($guid) { $this->personId = $guid; return $this; }
    public function setPersonNo($bool) { $this->personNo = $bool; return $this; }
    
    public function getCreatedOn()      { return $this->createdOn; }
    public function setCreatedOn($date) { $this->createdOn = $date; return $this; }
    
    public function __construct()
    {
        parent::__construct();
        
        $this->id = Guid::gen();
        
        $this->personNo = false;
        
        $this->createdOn = new \DateTime();
        
        $this->identifiers = new ArrayCollection();
    }
     
    /* ==========================================
     * Basic identifier stuff
     */
    public function addIdentifier($identifier)
    {
        foreach($this->identifiers as $identifierx)
        {
            if ($identifierx->getValue() == $identifier->getValue()) return $this;
        }
        $this->identifiers[] = $identifier;
        $identifier->setUser($this);
    }
    public function newIdentifier() { return new UserIdentifier(); }
    
    public function getIdentifiers() { return $this->identifiers; }
    
    /* ===============================================
     * Load stuff from array
     */
    public function loadFromArray($item)
    {
        // Keep it simple for now
        $props = array
        (
            'id'                 => 'id',
            'username'           => 'username',
            'usernameCanonical'  => 'username_canonical',
            'email'              => 'email',
            'emailCanonical'     => 'email_canonical',
            'enabled'            => 'enabled',
            'salt'               => 'salt',
            'password'           => 'password',
            'locked'             => 'locked',
            'expired'            => 'expired',
            'confirmationToken'  => 'confirmation_token',
            'credentialsExpired' => 'credentials_expired',
            'name'               => 'name',
            'personId'           => 'person_id',
            'personNo'           => 'person_no',
            
            'roles'              => 'roles',
        );
        foreach($props as $key => $value)
        {
            $this->$key = $item[$value];
        }
        // Handle dates
        $props = array
        (
            'createdOn'           => 'created_on',
            'lastLogin'           => 'last_login',
            'expiresAt'           => 'expires_at',
            'passwordRequestedAt' => 'password_requested_at',
            'credentialsExpireOn' => 'credentials_expire_at'
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
        // Identifiers
        foreach($item['identifiers'] as $identifierItem)
        {
            $identifier = $this->newidentifier();
            $identifier->loadFromArray($identifierItem);
            $this->addIdentifier($identifier);
        }
        return $this;
    }
}

?>
