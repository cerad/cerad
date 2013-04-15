<?php

namespace Cerad\Bundle\ArbiterBundle\Schedule\Tourn;

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
            $site = $game['site'];
            $subSite = null;
            
            $info = explode(',',$site);
            if (count($info) == 2)
            {
                $site    = trim($info[0]);
                $subSite = trim((string)$info[1]);
            }
            $data = array
            (
                $game['date'],
                $game['time'],
                $game['num'],
                $game['sport'],
                $game['level'],
                $game['home'],
                $game['level'],
                $game['away'],
                $game['level'],
                $site,$subSite,
            );
            fputcsv($file,$data);
        }
        
        fclose($file);
    }
}

?>
