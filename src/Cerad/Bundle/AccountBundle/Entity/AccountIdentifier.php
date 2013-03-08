<?php
namespace Cerad\Bundle\AccountBundle\Entity;

class AccountIdentifier
{    
    protected $id;
    
    protected $providerName;
    protected $identifier;
   
    protected $account;
    
    protected $status = 'Active';
    
    protected $profile;
    
    static public function create($providerName, $identifier, $profile = null)
    {
        $item = new self();
        
        $item->setProviderName($providerName);
        $item->setIdentifier  ($identifier);
        $item->setProfile     ($profile);
        
        return $item;
    }
    public function setStatus      ($value) { $this->status       = $value; }
    public function setAccount     ($value) { $this->account      = $value; }
    public function setProfile     ($value) { $this->profile      = $value; }
    public function setIdentifier  ($value) { $this->identifier   = $value; }
    public function setProviderName($value) { $this->providerName = $value; }
    
    public function getStatus()       { return $this->status;       }
    public function getAccount()      { return $this->account;      }
    public function getProfile()      { return $this->profile;      }
    public function getIdentifier()   { return $this->identifier;   }
    public function getProviderName() { return $this->providerName; }
}
?>