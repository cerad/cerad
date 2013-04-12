<?php
namespace Zayso\ArbiterBundle\Schedule;

use Zayso\ArbiterBundle\Entity\Game;

class SaveArbiterSchedule
{
    public function save($fileName,$games)
    {
        $file = fopen($fileName,'wt');
        
        $headers = array('Date', 'Time', 'Game', 'Sport', 'Level', 
            'Home-Team', 'Home-Level', 'Away-Team', 'Away-Level', 
            'Site', 'Sub-site', 'Bill-To', 'Officials',
        );
        fputcsv($file,$headers);
        
        foreach($games as $game)
        {
            $site = $game->getSite();
            $subSite = null;
            
            $info = explode(',',$site);
            if (count($info) == 2)
            {
                $site    = trim($info[0]);
                $subSite = trim((string)$info[1]);
            }
            $data = array
            (
                $game->getDate(),
                $game->getTime(),
                $game->getGameNum(),
                $game->getSport(),
                $game->getLevel(),
                $game->getHomeTeam(),
                $game->getLevel(),
                $game->getAwayTeam(),
                $game->getLevel(),
                $site,$subSite,
            );
            fputcsv($file,$data);
        }
        
        fclose($file);
    }
}

?>
