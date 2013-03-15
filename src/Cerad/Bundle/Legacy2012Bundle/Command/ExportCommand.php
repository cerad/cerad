<?php
namespace Cerad\Bundle\Legacy2012Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->exportAccounts();
        $this->exportPersons();
    }
    protected function exportAccounts()
    {
        $manager = $this->getService('cerad_legacy2012.account.manager');
        $accounts = $manager->findAll();
        
        echo sprintf("Accounts: %d %s\n",count($accounts),$accounts[0]->getUserName());
    }
    protected function exportPersons()
    {
        $manager = $this->getService('cerad_legacy2012.person.manager');
        $persons = $manager->findAll();
        
        echo sprintf("Persons: %d %s\n",count($persons),$persons[0]->getName());
    }
}

?>
