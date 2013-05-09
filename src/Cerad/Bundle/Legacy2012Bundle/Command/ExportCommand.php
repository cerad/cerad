<?php
namespace Cerad\Bundle\Legacy2012Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cerad\Bundle\PersonBundle\Entity\PersonCert;
use Cerad\Bundle\PersonBundle\Entity\PersonLeague;

class ExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('legacy2012:export')
            ->setDescription('Legacy 2012 Export')
          //->addArgument   ('inputFileName', InputArgument::REQUIRED, 'Input File Name')
          //->addArgument   ('truncate',      InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->exportPersons();
        $this->exportPersonPersons();
        $this->exportAccounts();
//      $this->exportGames();
    }
    protected $emails;
    protected $persons;
    
    protected function exportAccount($manager,$account2012)
    {   
        // Needs to be linked to a real person
        $person = $account2012->getPerson();
        if (!$person->getId())
        {
            echo sprintf("No person for: %d %s\n",$account2012->getId(),$account2012->getUserName());
            return;
        }
        $email = $person->getEmail();
        if (!$email)
        {
           echo sprintf("No email for: %d %s\n",$person->getId(),$person->getPersonName());
           return;  
        }
        $email = $manager->canEmail($email);
        
        if (isset($this->emails[$email]))
        {
            /* ===================================================
             * In most cases these are duplicate accounts
             * In some cases these are account for family members with the same email
             */
            $personx = $this->emails[$email];
            /*
            echo sprintf("DUP email for: \n   %d %s %s\n   %d %s %s\n",
                    $personx->getId(),$personx->getPersonName(),$personx->getEmail(),
                    $person->getId(), $person->getPersonName(), $person->getEmail()); */
            return;  
        }
        $this->emails[$email] = $person;
        
        // Xfer the account
        $account = $manager->createUser();
       
        // User name stays the same
        $username = $account2012->getUserName();
        $account->setUsername($username);
        
        $password = $account2012->getUserPass();
        $account->setPassword($password);
            
        $account->setEmail($email); 
        $account->setName($person->getPersonName());
        
        $person2016 = $this->persons[$person->getId()];
      //die($person2016->getId());
        $account->setPersonGuid($person2016->getId());
        
        $account->setEnabled(true);
        
        // Openids
        foreach($account2012->getOpenids() as $openid)
        {
            // Maybe pull some more profile info?
            $profile = array
            (
                
            );
            $identifier = $manager->createIdentifier($openid->getProvider(),$openid->getIdentifier(),$profile);
            $manager->addIdentifierToUser($account,$identifier);
        }
        // And persist
        $manager->updateUser($account,true);
    }
    protected function exportAccounts()
    {
        $manager2012  = $this->getService('cerad_legacy2012.account.manager');
        $account2012s = $manager2012->findAll();
        
      //$manager = $this->getService('cerad_account.user_manager');
        $manager = $this->getService('fos_user.user_manager');
        $manager->deleteUsers();
        
        foreach($account2012s as $account2012)
        {
            $this->exportAccount($manager,$account2012);            
        }
        
        echo sprintf("Accounts: %d %s\n",count($account2012s),$account2012s[0]->getUserName());
    }
    /* ==========================================================
     * Process person
     */
    protected function exportPerson($manager,$person2012)
    {
        $person = $manager->newPerson();
        $person->setIdx      ($person2012->getId());
        $person->setName     ($person2012->getName());
        $person->setLastName ($person2012->getLastName());
        $person->setNickName ($person2012->getNickName());
        $person->setFirstName($person2012->getFirstName());
        $person->setEmail    ($person2012->getEmail());
        $person->setPhone    ($person2012->getCellPhone());
        $person->setGender   ($person2012->getGender());
      
        // Convert dob to date time, like to clear out time, probably not needed
        $dob = $person2012->getDob();
        $dob = substr($dob,0,4) . '-' . substr($dob,4,2) . '-' . substr($dob,6,2) . ' 00:00:00';
      //die($dob . "\n");
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s',$dob);
      //echo get_class($dt); die("\n");
        if ($dt) $person->setDob($dt);
        
        // Certs
        foreach($person2012->getRegisteredPersons() as $reg)
        {
            $identifier = $reg->getRegKey();
            
            // Each person belons to at least one league
            $league = PersonLeague::createVolunteerAYSO();
            $league->setPerson($person);
            $league->setLeague($reg->getOrgKey());
            $league->setIdentifier($identifier);
            $league->setMemId(substr($identifier,5));
            $league->setMemYear($reg->getMemYear());
            $league->setCvpa($reg->getSafeHaven());
            
            /* Safe Haven
            $safeHaven = $reg->getSafeHaven();
            if ($safeHaven)
            {
                switch($safeHaven)
                {
                    case 'Yes':     break;
                    case 'None':    break;
                    case 'AYSO':    break;
                    case 'Youth':   break;
                    case 'Coach':   break;
                    case 'Referee': break;
                    default: die($safeHaven . "\n");
                }
            }*/
            $manager->persist($league);
            
            // Referee Certification
            $badge = $reg->getRefBadge();
            if ($badge)
            {
                $cert = PersonCert::createRefereeAYSO();
                $cert->setPerson($person);
                $cert->setIdentifier($identifier);
                $cert->setBadge ($badge);
                $cert->setBadgex($badge);
                
                $date = $reg->getRefDate();
                $date = substr($date,0,4) . '-' . substr($date,4,2) . '-' . substr($date,6,2) . ' 00:00:00';
                $dt = \DateTime::createFromFormat('Y-m-d H:i:s',$date);
                
                if ($dt) $cert->setDateFirstCertified($dt);
                if ($dt) $cert->setDateLastUpgraded  ($dt);
                
                $manager->persist($cert);
            }
        }
        // Every person relates to themselves
        $personPerson = $manager->newPersonPerson();
        $personPerson->setMaster($person);
        $personPerson->setSlave ($person);
        $manager->persist($personPerson);
        
        // Persist
        $manager->persist($person);
        $manager->flush();
        
        // Tuck the guid away
      //$this->persons[$person2012->getId()] = $person->getId();
        $this->persons[$person2012->getId()] = $person;
        
        return;
    }
    protected function exportPersons()
    {
        $manager2012 = $this->getService('cerad_legacy2012.person.manager');
        $person2012s = $manager2012->findAll();
        
        $manager = $this->getService('cerad_person.manager');
        $manager->deletePersons();
        
        foreach($person2012s as $person2012)
        {
            $this->exportPerson($manager,$person2012);
        }
        echo sprintf("Persons: %d %s\n",count($person2012s),$person2012s[0]->getName());
    }
    protected function exportPersonPersons()
    {
        $manager2012 = $this->getService('cerad_legacy2012.person.manager');
        $personPerson2012s = $manager2012->findAllPersonPersons();
        
        $manager = $this->getService('cerad_person.manager');
       
        foreach($personPerson2012s as $personPerson2012)
        {
            $masterId = $personPerson2012->getPerson1()->getId();
            $slaveId  = $personPerson2012->getPerson2()->getId();
            if ($masterId != $slaveId)
            {
                $master = $this->persons[$masterId];
                $slave  = $this->persons[$slaveId];
                $role   = $personPerson2012->getRelation();
                
                $personPerson = $manager->newPersonPerson();
                $personPerson->setMaster($master);
                $personPerson->setSlave ($slave);
                $personPerson->setRole  ($role);
                $manager->persist($personPerson);
            }
        }
        $manager->flush();
    }
    /* ==================================================================
     * Game stuff
     */
    protected function exportGames()
    {
        $manager = $this->getService('cerad_legacy2012.game.manager');
        
        $games = $manager->loadGamesForDate('20120706');
        $game  = $games[0];
        $gameHomeTeam = $game->getHomeTeam();
        $homeTeam = $gameHomeTeam->getTeam();
        echo sprintf("Games: %d %s %s %s %s %s ",count($games),
                $game->getDate(),$game->getFieldDesc(),$game->getPool(),
                $gameHomeTeam->getType(),$homeTeam->getDesc1());
        
        $eventPersons = $game->getEventPersons();
        foreach($eventPersons as $eventPerson)
        {
            echo $eventPerson->getPersonName() . ' ';
        }
        echo "\n";
        
    }
}

?>
