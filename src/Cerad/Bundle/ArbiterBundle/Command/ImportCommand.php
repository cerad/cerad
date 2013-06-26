<?php
namespace Cerad\Bundle\ArbiterBundle\Command;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('arbiter:import')
            ->setDescription('Arbiter Import')
            ->addArgument   ('inputFileName', InputArgument::REQUIRED, 'Input File Name')
            ->addArgument   ('truncate',      InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputFileName = $input->getArgument('inputFileName');
        $truncate      = $input->getArgument('truncate');
        
        if ($truncate) $truncate = true;
        
      //echo sprintf("Import %s %d\n",$inputFileName,$truncate);
        
        $this->loadFile($inputFileName,$truncate);
        
        //echo $inputFileName . "\n";
    }
    protected function loadFile($file, $truncate = false)
    {
        switch(substr($file,0,4))
        {
            case 'Naso': $domain = 'NASOA'; break;
            case 'Alys': $domain = 'ALYS';  break;
            default:
                return;
        }
        $datax  = $this->getParameter('cerad_datax');
        $sport  = $this->getParameter('cerad_default_sport');
        $season = $this->getParameter('cerad_default_season');
        
        $inputFileName = sprintf('%s/arbiter/%s/%s',$datax,$season,$file);
        $params = array
        (
            'truncate' => $truncate,
            'output'   => 'Post', // Post, Scan, Excel
            'source'   => 'Arbiter',
            'sport'    => $sport,
            'season'   => $season,
            'domain'   => $domain,
            
            'defaultGameStatus' => 'Normal',
            'inputFileName'     => $inputFileName,
        );
        $import = $this->getService('cerad_arbiter.schedule.import.master');
        
        $results = $import->importFile($params);
        
        echo sprintf("Import Complete %-5s %s\n",$domain,$params['inputFileName']);
        echo $results;
    }
}

?>
