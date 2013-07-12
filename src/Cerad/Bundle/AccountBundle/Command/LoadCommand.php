<?php
namespace Cerad\Bundle\AccountBundle\Command;

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
class LoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:account:load')
            ->setDescription('Load accouts from yaml file')
             ->addArgument   ('fileName', InputArgument::REQUIRED, 'File to load')
             ->addArgument   ('arg1',     InputArgument::OPTIONAL, 'arg1(optional)')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = $input->getArgument('fileName');
        $arg1     = $input->getArgument('arg1');
        
        $accounts = Yaml::parse($fileName);
        echo sprintf("Loaded %d\n",count($accounts));
        
        $manager = $this->getService('cerad_account.user_manager');
        $manager->deleteUsers();
        
      //$accountsx[] = $accounts[1];
        
        foreach($accounts as $account)
        {
            $user = $manager->createUser();
            $user->loadFromArray($account);
            $manager->updateUser($user,true);
        }
        return;
    }        
}
?>
