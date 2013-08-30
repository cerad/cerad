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
class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:person:test')
            ->setDescription('Test Command');
          //->addArgument   ('fileName', InputArgument::REQUIRED, 'File to load')
          //->addArgument   ('arg1',     InputArgument::OPTIONAL, 'arg1(optional)')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $repo = $this->getService('cerad_person.repository');
        
        $items = $repo->findAllNames();
        print_r($items);
        return;
        
        $person = $items[0];
        
        echo sprintf("Item count %d %s %s %s\n",count($items),get_class($person),$person->getName(),$person->getLastName());
        
        $certs   = $person->getCerts();
        $leagues = $person->getLeagues();
        echo sprintf("Cert class %s\n",get_class($certs));
      //$certs->setInitialized(true);
      //
        echo sprintf("Cert count %d\n",count($certs));
      //$cert = $certs[0];
      //echo sprintf("Cert count %d %d %s\n",count($certs),$cert->getId(),$cert->getRole());
      //echo sprintf("Leag count %d\n",count($leagues));
         
        return;
    }        
}
?>
