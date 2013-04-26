<?php
namespace Cerad\Bundle\AccountBundle\Security;

use FOS\UserBundle\Security\UserProvider as BaseUserProvider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * 
 */
class AccountUserProvider extends BaseUserProvider
{
    public function __construct($userManager, $personManager = null)
    {
        parent::__construct($userManager);
        
        $this->personManager = $personManager;
    }

    protected function findUser($username)
    {
        // Check AccountUser
        $user = $this->userManager->findUserByUsernameOrEmail($username);
        
        if (!$user) $user = $this->userManager->findUserByIdentifier($username);
        
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
 
        // Load in person
        if (!$this->personManager) return $user;
        
        $person = $this->personManager->find($user->getPersonGuid());
        
        $user->setPerson($person);
        
        return $user;
        //die($person->getName());
    }
    public function refreshUserx(SecurityUserInterface $user)
    {
        //die('refresh');
        return $user;
    }
}

?>
