<?php
namespace Cerad\Bundle\AccountBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//  Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;

class DumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('cerad:account:dump')
            ->setDescription('Some Cerad Account Tests')
          //->addArgument   ('testName', InputArgument::REQUIRED, 'Test to run')
          //->addArgument   ('arg1',     InputArgument::OPTIONAL, 'arg1(optional)')
       ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $now = $now->format('Y-m-d H:i:00');
        
        $conn  = $this->getService('doctrine.dbal.accounts_connection');
        $items = $conn->fetchAll('SELECT * FROM account_user');
        $users = array();
        foreach($items as $item)
        {
            $id = $item['id'];
            $item['id'] = $this->guid();
            
            $item['person_id'] = $item['person_guid'];
            unset($item['person_guid']);
            $item['person_no'] = 0;
            
            $roles = unserialize($item['roles']);
            $item['roles'] = $roles;
            
            $item['created_on'] = $now;
            
            $item['identifiers'] = array();
            
            $users[$id] = $item;
        }
        file_put_contents('accounts.yml',Yaml::dump($users));
        
        $items = $conn->fetchAll('SELECT * FROM account_identifier');
        foreach($items as $item)
        {
            $user = &$users[$item['account_id']];
            
            $itemx = array();
            
            $itemx['id']      = $this->guid();
            $itemx['user_id'] = $user['id'];
            
            $itemx['source'] = $item['provider_name'];
            $itemx['value']  = $item['identifier'];
            
            $itemx['name']   = $item['display_name'];
             
            $itemx['status']  = $item['status'];
            $itemx['profile'] = unserialize($item['profile']);
            $itemx['created_on'] = $now;
            
            if (!$itemx['name']) $itemx['name'] = $user['name'];
            
            // And store
            $user['identifiers'][] = $itemx;
        }        
        file_put_contents('accounts.yml',Yaml::dump($users,10));
      //file_put_contents('idents.yml',  Yaml::dump($items,10));
        echo sprintf("Dump command %d\n",count($users));
    }
    protected function guid()
    {        
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                mt_rand(0, 65535),     mt_rand(0, 65535),     mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), 
                mt_rand(0, 65535),     mt_rand(0, 65535));
    }
}
?>
