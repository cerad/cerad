<?php
namespace Cerad\Bundle\AccountBundle\Security;

use FOS\UserBundle\Security\UserProvider as BaseUserProvider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * 
 * An optional personManager will attach any person
 * Also allows signing in by membership id.
 * 
 */
class AccountUserProvider extends BaseUserProvider
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
        $guid = $user->getPersonGuid();
        if (!$guid) return;
        
        $person = $this->personManager->find($guid);
        
        $user->setPerson($person);        
    }
    protected function findUser($username)
    {
        // Check AccountUser
        $user = $this->userManager->findUserByUsernameOrEmail($username);
        
        if (!$user) $user = $this->userManager->findUserByIdentifier($username);
        
        // Check for unique league id
        if (!$user && $this->personManager)
        {
            $person = $this->personManager->loadPersonForLeagueMemId($username);
            if ($person)
            {
                $user = $this->userManager->findUserByPerson($person->getId());
            }
          //else die('no league');
        }
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
 
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
