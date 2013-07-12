<?php
namespace Cerad\Bundle\AccountBundle\Security;

use FOS\UserBundle\Security\UserProvider as UserProviderBase;

//  Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * 
 * An optional personManager will attach any person
 * Also allows signing in by membership id.
 * 
 */
class UserProvider extends UserProviderBase
{
    public function __construct($userManager, $personManager = null)
    {
        parent::__construct($userManager);
        
        $this->personManager = $personManager;
    }
    protected function addPersonToUser($user)
    {
        // Load in person
        if (!$this->personManager) return;

        // Null searches cause problems
        $personId = $user->getPersonId();
        if (!$personId) return;
        
        $person = $this->personManager->find($personId);
        
        $user->setPerson($person);
    }
    /* ========================================================================
     * Note that the loadUserByUsername is defined Security/UserProvider
     * It calls findUser and throws an username not foud exception
     * 
     * For some reason, findUser was made protected.  Kind of handy to have.
     * Hence by own fundUserByUsername
     * 
     * Maybe this should be in the user manager?
     */
    public function findUserByUsername($username) { return $this->findUser($username); }
    protected function findUser($username)
    {
        // Check AccountUser
        $user = $this->userManager->findUserByUsernameOrEmail($username);
       
        if (!$user) $user = $this->userManager->findUserByIdentifierValue($username);
        
        // Check for unique league id
        if (!$user && $this->personManager)
        {
            $person = $this->personManager->findByIdentifierValue($username);
            if ($person)
            {
                $user = $this->userManager->findUserByPersonId($person->getId());
            }
        }
        if (!$user) return null;
 
        $this->addPersonToUser($user);
        
        return $user;
    }
    public function refreshUser(SecurityUserInterface $user)
    {
        $user = parent::refreshUser($user);
        
        $this->addPersonToUser($user);
        
        return $user;
    }
}

?>
