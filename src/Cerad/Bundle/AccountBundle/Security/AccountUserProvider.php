<?php
namespace Cerad\Bundle\AccountBundle\Security;

use FOS\UserBundle\Security\UserProvider as BaseUserprovider;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * 
 */
class AccountUserProvider extends BaseUserProvider
{
    protected function findUser($username)
    {
        // Check AccountUser
        $user = $this->userManager->findUserByUsernameOrEmail($username);
        if ($user) return;
        
        return $this->userManager->findUserByIdentifier($username);
 
        // Check AccountIdentifier
        // Maybe move to UserManager?
        $qb = $this->userManager->repository->createQueryBuilder('user');
        $qb->leftJoin('user.identifiers','identifier');
        $qb->where('identifier.identifier = ?1');
        $qb->setParameter(1,$username);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    
}

?>
