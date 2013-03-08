<?php
namespace Cerad\Bundle\AccountBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;

use FOS\UserBundle\Util\CanonicalizerInterface;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * 
 */
class AccountUserManager extends BaseUserManager
{
    protected $accountUserClassName;
    protected $accountIdentifierClassName;
    
    public function __construct(
            EncoderFactoryInterface $encoderFactory, 
            CanonicalizerInterface  $usernameCanonicalizer, 
            CanonicalizerInterface  $emailCanonicalizer, 
            ObjectManager           $om, 
            $accountUserClassName,
            $accountIdentifierClassName)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer,$om,$accountUserClassName);
        
        $this->accountUserClassName       = $accountUserClassName;
        $this->accountIdentifierClassName = $accountIdentifierClassName;
    }
    public function findUserByIdentifier($identifier)
    {
        $qb = $this->objectManager->createQueryBuilder();
        $qb->select('accountIdentifier, accountUser');
        $qb->from($this->accountIdentifierClassName, 'accountIdentifier');

        $qb->leftJoin('accountIdentifier.account','accountUser');
        $qb->where   ('accountIdentifier.identifier = ?1');
        $qb->setParameter(1,$identifier);
        
        $accountIdentifier = $qb->getQuery()->getOneOrNullResult();
        if ($accountIdentifier) return $accountIdentifier->getAccount();
        
        return null;
    }
    public function createIdentifier($providerName,$identifier,$profileData = null)
    {
        $accountIdentifier = new $this->accountIdentifierClassName;
        $accountIdentifier->setProviderName($providerName);
        $accountIdentifier->setIdentifier  ($identifier);
        $accountIdentifier->setProfile     ($profileData);
        return $accountIdentifier;
    }
    public function addIdentifierToUser($user,$identifier)
    {
        $user->addIdentifier($identifier);
    }

}

?>
