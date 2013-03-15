<?php

namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class AccountOpenid extends BaseEntity
{
    protected $id;
    
    protected $identifier = null;

    protected $provider = null;

    protected $account = null;

    protected $status = 'Active';

    protected $displayName = null;

    protected $userName = null;

    protected $email = null;
    
    // Loads some stuff from return profile
    public function setProfile($data)
    {
        if (isset($data['identifier']))        $this->setIdentifier ($data['identifier']);
        if (isset($data['providerName']))      $this->setProvider   ($data['providerName']);
        if (isset($data['displayName']))       $this->setDisplayName($data['displayName']);
        if (isset($data['preferredUsername'])) $this->setUserName   ($data['preferredUsername']);
        if (isset($data['verifiedEmail']))     $this->setEmail      ($data['verifiedEmail']);
    }
    public function setAccount($account)         { $this->onObjectPropertySet('account',$account); }
    public function getAccount()                 { return $this->account; }

    public function setStatus($status)           { $this->onScalerPropertySet('status',$status); }
    public function getStatus()                  { return $this->status; }

    public function setDisplayName($displayName) { $this->onScalerPropertySet('displayName',$displayName); }
    public function getDisplayName()             { return $this->displayName; }
    
    public function setUserName($userName)       { $this->onScalerPropertySet('userName',$userName); }
    public function getUserName()                { return $this->userName; }
    
    public function setEmail($email)             { $this->onScalerPropertySet('email',$email); }
    public function getEmail()                   { return $this->email; }

    public function setProvider($provider)       { $this->onScalerPropertySet('provider',$provider); }
    public function getProvider()                { return $this->provider; }
    
    public function setIdentifier($identifier)   { $this->onScalerPropertySet('identifier',$identifier); }
    public function getIdentifier()              { return $this->identifier; }
    
    public function getId() { return $this->id; }
}