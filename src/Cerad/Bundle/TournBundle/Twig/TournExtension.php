<?php
namespace Cerad\Bundle\TournBundle\Twig;

class TournExtension extends \Twig_Extension
{
    protected $env;
    protected $project;
    
    public function getName()
    {
        return 'cerad_schedule_extension';
    }
    public function __construct($project)
    {
        $this->project = $project;
    }
    public function initRuntime(\Twig_Environment $env)
    {
        parent::initRuntime($env);
        $this->env = $env;
    }
    protected function escape($string)
    {
        return twig_escape_filter($this->env,$string);
    }
    public function getFunctions()
    {
        return array(            
            'cerad_tourn_show_header' => new \Twig_Function_Method($this, 'showHeader'),
            
            'cerad_tourn_get_project_title'       => new \Twig_Function_Method($this, 'getProjectTitle'),
            'cerad_tourn_get_project_description' => new \Twig_Function_Method($this, 'getProjectDescription'),
            
            'cerad_tourn_schedule_referee_list_csv' => new \Twig_Function_Method($this, 'genScheduleRefereeListCSV'),
            'cerad_tourn_schedule_referee_list_xls' => new \Twig_Function_Method($this, 'genScheduleRefereeListXLS'),
        );
    }
    public function genScheduleRefereeListXLS($games,$excel)
    {
        
    }
    public function genScheduleRefereeListCSV($games)
    {
        $fp = fopen('php://temp','r+');

        // Header
        $row = array(
            "Game","Date","DOW","Time","Field",
            "Pool","Home Team","Away Team",
            "Referee","Asst Referee 1","Asst Referee 2",
        );
        fputcsv($fp,$row);

        // Games is passed in
        foreach($games as $game)
        {
            // Date/Time
            $dt   = $game->getDtBeg();
            $dow  = $dt->format('D');
            $date = $dt->format('M d');
            $time = $dt->format('g:i A');
            
            // Build up row
            $row = array();
            $row[] = $game->getNum();
            $row[] = $date;
            $row[] = $dow;
            $row[] = $time;
            $row[] = $game->getField()->getName();
    
            $row[] = $game->getPool() . $game->getLevel()->getName();
            $row[] = $game->getHomeTeam()->getName();
            $row[] = $game->getAwayTeam()->getName();
    
            foreach($game->getPersons() as $gamePerson)
            {
                $row[] = $gamePerson->getName();
            }
            fputcsv($fp,$row);
        }
        // Return the content
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        //echo $csv; //die();
        return $csv;
    }
    public function getProjectDescription()
    {
        return $this->project->getDesc();
    }
    public function getProjectTitle()
    {
        return $this->project->getTitle();
    }
    public function showHeader()
    {
        if (defined('CERAD_TOURN_SHOW_HEADER')) return CERAD_TOURN_SHOW_HEADER;
        return true;
    }
}
?>
