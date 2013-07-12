<?php
namespace Cerad\Bundle\AccountBundle\Security;

use Doctrine\Common\Persistence\ObjectManager;

use FOS\UserBundle\Util\CanonicalizerInterface;
use FOS\UserBundle\Doctrine\UserManager as UserManagerBase;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/* ===============================================
 * Want to avoid having any special interface just for janrain or oauth2
 * FOS\UserBundle\Model\UserManager
 * FOS\UserBundle\Doctrine\UserManager - Adds repo
 * 
 */
class UserManager extends UserManagerBase
{
    protected $userClassName;
    protected $userIdentifierClassName;
    
    public function __construct(
            EncoderFactoryInterface $encoderFactory, 
            CanonicalizerInterface  $usernameCanonicalizer, 
            CanonicalizerInterface  $emailCanonicalizer, 
            ObjectManager           $om, 
            $userClassName,
            $userIdentifierClassName)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer,$om,$userClassName);
        
        $this->userClassName       = $userClassName;
        $this->userIdentifierClassName = $userIdentifierClassName;
    }
    public function findUserByIdentifierValue($value)
    {
        // TODO: Replace with DQL?
        $qb = $this->objectManager->createQueryBuilder();
        $qb->select('identifier, user');
        $qb->from($this->userIdentifierClassName, 'identifier');
        $qb->leftJoin('identifier.user','user');
        $qb->where   ('identifier.value = ?1');
        $qb->setParameter(1,$value);
        
        $identifier = $qb->getQuery()->getOneOrNullResult();
        if ($identifier) return $identifier->getUser();
        
        return null;
    }
    public function addIdentifierToUser($user,$identifier)
    {
        $user->addIdentifier($identifier);
    }
    public function findUserByPersonId($personId)
    {
        return $this->findUserBy(array('personId' => $personId));
    }

    /* =======================================================
     * Mostly for testing, delete all accounts
     */
    public function deleteUsers()
    {
        $conn = $this->objectManager->getConnection();
        $conn->executeUpdate('DELETE FROM account_identifier;');
        $conn->executeUpdate('DELETE FROM account_user;');
        
      //$conn->executeUpdate('ALTER TABLE account_identifier AUTO_INCREMENT = 1;');
      //$conn->executeUpdate('ALTER TABLE account_user       AUTO_INCREMENT = 1;');
    }
    // Really should not need these?  Used by validators
    // Replace with updateCanonicalFields
    public function canEmailx($email)
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }
    public function canUsernamex($username)
    {
        return $this->usernameCanonicalizer->canonicalize($username);
    }

}

?>
