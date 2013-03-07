<?php

namespace Cerad\Bundle\JanrainBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/* ====================================================================
 * Should this derive from an Abrstract class?
 */
class JanrainAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $providerKey;
    
    public function __construct(UserProviderInterface $userProvider, $providerKey)
    {
        $this->userProvider = $userProvider;
        $this->providerKey  = $providerKey;
    }

    // Comes in with JanrainAuthenticationToken
    public function authenticate(TokenInterface $token)
    {
        // die('AuthenticationProvider.authenticate ' . get_class($this->userProvider));
        
        // Identifier might include resource owner name (google/facebook etc)
        $identifier = $token->getIdentifier();
        
        // Tosses a UsernameNotFound exception
        $user = $this->userProvider->loadUserByUsername($identifier);

        // Probably want to change this to JanraonToken?
        // Want to add profile/accessToken?
        $authToken = new UsernamePasswordToken($user,null,$this->providerKey,$user->getRoles());
        
        // Need to add a userChecker here?
        
        return $authToken;
    }

    public function supports(TokenInterface $token)
    {
        // Gets called outside of fire wall
        if ($token instanceof RememberMeToken ) return false;
        
        // Get this with no roles in CeradUser
        if ($token instanceof UsernamePasswordToken ) return false;
        
        if ($token instanceof JanrainAuthenticationToken ) return true;
        
        return false;
        
      //die('CeradAuthenticationProvider.supports ' . get_class($token));
    }
}

?>
