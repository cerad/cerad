<?php
namespace Cerad\Bundle\JanrainBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/* ---------------------------------------------------
 * This is strictly a holder for janrain auth info profile
 */
class JanrainAuthenticationToken extends AbstractToken
{
    protected $profile;
    
    public function __construct($profile, array $roles = array())
    {
        parent::__construct($roles);

        $this->profile  = $profile;
    }
    public function getIdentifier()
    {
        return $this->profile->getIdentifier();
    }
    public function getCredentials()
    {
        return null;
    }
    // So it can be stored in session
    // Needs to call the parent as well
    // Maybe not, right now this token never get's serialized
    public function serialize()
    {
        return serialize(array($this->provider,$this->identifier,$this->userName,$this->email,$this->id,$this->profile));
    }

    public function unserialize($str)
    {
        list($this->provider,$this->identifier,$this->userName,$this->email,$this->id,$this->profile) = unserialize($str);
    }
}
?>
