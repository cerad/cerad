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
    public function createIdentifier($providerName,$displayName,$identifier,$profileData = null)
    {
        $accountIdentifier = new $this->accountIdentifierClassName;
        $accountIdentifier->setProviderName($providerName);
        $accountIdentifier->setDisplayName ($displayName);
        $accountIdentifier->setIdentifier  ($identifier);
        $accountIdentifier->setProfile     ($profileData);
        return $accountIdentifier;
    }
    public function addIdentifierToUser($user,$identifier)
    {
        $user->addIdentifier($identifier);
    }
    public function findUserByPerson($person)
    {
        return $this->findUserBy(array('personGuid' => $person));
    }

    /* =======================================================
     * Mostly for testing, delete all accounts
     */
    public function deleteUsers()
    {
        $conn = $this->objectManager->getConnection();
        $conn->executeUpdate('DELETE FROM account_identifier;');
        $conn->executeUpdate('DELETE FROM account_user;');
        
        $conn->executeUpdate('ALTER TABLE account_identifier AUTO_INCREMENT = 1;');
        $conn->executeUpdate('ALTER TABLE account_user       AUTO_INCREMENT = 1;');
    }
    public function canEmail($email)
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }
    public function canUsername($username)
    {
        return $this->usernameCanonicalizer->canonicalize($username);
    }

}

?>
