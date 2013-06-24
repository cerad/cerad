<?php
namespace Cerad\Bundle\Legacy2012Bundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

//use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cerad\Bundle\PersonBundle\Entity\PersonCert;
use Cerad\Bundle\PersonBundle\Entity\PersonLeague;

/* =================================================================
 * For 2012 I used org (short for organiziation) for ayso regions
 * For 2016 I renamed this to league
 * AYSO Region, Area, Section, National
 * HASL - Huntsville Adult Soccer League
 * NASL - North Alabama Soccer League
 * High School Teams (Varsity JV etc)
 * HFC - Huntsville Futbal Club
 * VFC - Valley Futbal Club
 * USSF Alabama
 * USSF Tennesse
 * NFHS Alabama
 * NFHS Tennesse
 * 
 * Still not a real good name
 */
class LeagueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('legacy2012:league')
            ->setDescription('Legacy 2012 League')
          //->addArgument   ('inputFileName', InputArgument::REQUIRED, 'Input File Name')
          //->addArgument   ('truncate',      InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->exportLeagues();
    }
    protected function exportLeagues()
    {
        $manager2012  = $this->getService('cerad_legacy2012.org.manager');
        $org2012s     = $manager2012->findAll();
        
        $manager = $this->getService('cerad_game.league.manager');
        $manager->deleteLeagues();
        
        // One pass without parents
        foreach($org2012s as $org2012)
        {
            $this->exportLeague($manager,$org2012);            
        }
        $manager->flush();
        
        // One pass for parents
        foreach($org2012s as $org2012)
        {
            $this->exportLeagueParent($manager,$org2012);            
        }
        $manager->flush();
       
        echo sprintf("League 2012 Count: %d\n",count($org2012s));
    }
    /* ==========================================================
     * Process League
     * First pass does not set the parent
     */
    protected function exportLeague($manager,$org2012)
    {
        $league = $manager->newLeague();
        
        $league->setId    ($org2012->getId    ());
        $league->setDesc1 ($org2012->getDesc1 ());
        $league->setDesc2 ($org2012->getDesc2 ());
        $league->setCity  ($org2012->getCity  ());
        $league->setState ($org2012->getState ());
        $league->setStatus($org2012->getStatus());
        
        $manager->persist($league);
    }
    /* ==========================================================
     * Process League
     * First pass does not set the parent
     */
    protected function exportLeagueParent($manager,$org2012)
    {
        // First get the parent id aka key
        $parent2012 = $org2012->getParent();
        if (!$parent2012) return;
        $parentId = $parent2012->getId();
        
        // Grab the new parent object
        $parentId = $parent2012->getId();
        $parent   = $manager->find($parentId);
        if (!$parent) die('No parent for ' . $parentId);
        
        // Grab the object
        $leagueId = $org2012->getId();
        $league = $manager->find($org2012->getId());
        if (!$league) die('No league for ' . $leagueId);
        
        $league->setParent($parent);
    }
}

?>
