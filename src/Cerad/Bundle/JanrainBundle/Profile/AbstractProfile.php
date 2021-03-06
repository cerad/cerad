<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

class AbstractProfile
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function getData() { return $this->data; }
    
    public function getIdentifier()  { return $this->data['identifier'];   }
    
    // These could be marked as abstract
    public function getIdentifier2() { return null; }
    
    public function getProviderName() { return $this->data['providerName']; }
    
    public function getVerifiedEmail()
    {
        return isset($this->data['verifiedEmail']) ?  $this->data['verifiedEmail'] : null;
    }
    public function getEmail()
    {
        if (isset($this->data['verifiedEmail']) && $this->data['verifiedEmail']) return $this->data['verifiedEmail'];
        if (isset($this->data['email'        ]) && $this->data['email'])         return $this->data['email'];
        return null;
    }
    public function getName()
    {
        if (!isset($this->data['name'])) return null;
        if ( isset($this->data['name']['formatted'])) return $this->data['name']['formatted'];
        return null;
    }
    public function getUsername()
    {
        return isset($this->data['preferredUsername']) ? $this->data['preferredUsername'] : null;
    }
    public function getDisplayName()
    {
        return isset($this->data['displayName']) ? $this->data['displayName'] : null;
    }
}
/*
 * [providerName]      => Facebook 
 * [identifier]        => http://www.facebook.com/profile.php?id=1530263836 
 * [verifiedEmail]     => ahundiak@gmail.com 
 * [preferredUsername] => ArthurHundiak 
 * [displayName]       => Arthur Hundiak 
 * [name] => Array ( 
 *     [formatted]  => Arthur Hundiak 
 *     [givenName]  => Arthur 
 *     [familyName] => Hundiak 
 * [email]     => ahundiak@gmail.com 
 * [url]       => http://www.facebook.com/arthur.hundiak 
 * [photo]     => https://graph.facebook.com/1530263836/picture?type=large 
 * [utcOffset] => -05:00 
 * [address]
 *     [formatted] => Huntsville, Alabama 
 *     [type]      => currentLocation 
 * [gender] => male
 * 
 */

?>
