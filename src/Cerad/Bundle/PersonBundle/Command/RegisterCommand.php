<?php
namespace Cerad\Bundle\PersonBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

/* =============================================================
 * Read in an accounts.yml file and load the accounts tables
 * Try to minimize processing as much as possible
 */
class RegisterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:person:register')
            ->setDescription('Register');
          //->addArgument   ('fileName', InputArgument::REQUIRED, 'File to load')
          //->addArgument   ('arg1',     InputArgument::OPTIONAL, 'arg1(optional)')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getService('cerad_person.repository');
        
        $person = $repo->newPerson();
        
        $person->setName     ('Art Hundiak');
        $person->setFirstName('Arthur');
        $person->setLastName ('Hundiak');
        $person->setNickName ('Hondo');
        
        $person->setEmail('ahundiak@gmail.com');
        $person->setPhone('2564575943');
        $person->setCity ('Huntsville');
        $person->setState('AL');
        $person->setGender('M');
        $person->setDob   (new \DateTime('1958-06-05'));
        
        $regData = array(
            'person'    => $person,
            'badge'     => 'Grade_8',
            'ussfid'    => 'USSFC1234123412340001',
            'fedId'     => 'USSF',
            'orgId'     => 'USSFS_AL',
            'upgrading' => 'No',
        );
        $plan = $this->register($repo,$regData);        
    }
    /* ===============================================
     * Lot's of possible processing to do
     * All ends with a plan
     */
    protected function register($repo,$data)
    {
        // Have a person identifier?
        $person = $data['person'];
        $ussfid = $data['ussfid'];
        
        if (strlen($ussfid) != 21)
        {
            $ussfid = 'USSFFake' . uniqid();
        }
        $personFed = $repo->findFed($ussfid);
        if ($personFed)
        {
            // Have an existing record
            $person = $personFed->getPerson();
                
            // Could check certain fields for updates
        }
        else
        {
            $personFed = $person->getFedUSSFC();
            $personFed->setId($ussfid);
        }
        
        $cert = $personFed->getCertReferee();
        $cert->setBadgex   ($data['badge']);
        $cert->setUpgrading($data['upgrading']);
                
        $org = $personFed->getOrgState();
        $org->setOrgId($data['orgId']);
        
        $repo->persist($person);
        $repo->flush();
        
        return;
        
        $league = $person->getLeagueUSSFContractor();
        $league->setIdentifier($ussfid);
        $league->setLeague($formData['league']);
        
        $person->getPersonPersonPrimary();
        
        $repo->persist($person);
        $repo->flush();
       
        echo 'USSFID ' . $ussfid;
        return null;
    }    
}
?>
