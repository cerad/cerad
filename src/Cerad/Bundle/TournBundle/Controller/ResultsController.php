<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ResultsController extends Controller
{
    public function poolplayAction(Request $request, $div, $poolFilter)
    { 
        // For list of genders, ages and project_key
        $project = $this->get('cerad_tourn.project');
        
        // Pull div from session
        if (!$div)
        {
            $data = $request->getSession()->get('cerad_tourn_results_poolplay');
            if (is_array($data) && isset($data['div']) && $data['div'])
            {
                // A redirect seems to be the cleanest
                return $this->redirect($this->generateUrl('cerad_tourn_results_poolplay',$data));
            }
        }
        else $request->getSession()->set('cerad_tourn_results_poolplay',array('div' => $div, 'poolFilter' => $poolFilter));
        
        // Grab games
        if (strlen($div) == 4)
        {
            $age    = substr($div,0,3);
            $gender = substr($div,3,1);
            $params = array
            (
                'projects' => array($project->getKeySearch()),
                'ages'     => array($age),
                'genders'  => array($gender),
                'gameTypes'=> array('PP'),    // Stored in game.pool, game.level contains the bracket
            );
            if ($gender == 'B') $params['genders'][] = 'C';
            
            $scheduleManager = $this->get('cerad_tourn.schedule.manager');
            $games = $scheduleManager->loadGames($params);
        }
        else $games = array();
        
        $resultsManager = $this->get('cerad_tourn.results.manager');
        $pools = $resultsManager->getPools($games,$poolFilter);
        
        $ages    = $project->getAges();
        $genders = $project->getGenders();
        
        // Render
        $tplData = array();
        $tplData['ages']    = $ages['choices'];
        $tplData['genders'] = $genders['choices'];
        $tplData['route']   = 'cerad_tourn_results_poolplay';
        $tplData['pools']   = $pools;
        $tplData['games']   = $games; // Only for debugging
        return $this->render('@CeradTourn/results/poolplay/index.html.twig', $tplData);
    }
    public function playoffsAction(Request $request)
    {   
        $tplData = array();
        return $this->render('@CeradTourn/results/playoffs/index.html.twig', $tplData);
    }
    public function championsAction(Request $request)
    {   
        $tplData = array();
        return $this->render('@CeradTourn/results/champions/index.html.twig', $tplData);
    }
    public function sportsmanshipAction(Request $request)
    {   
        $tplData = array();
        return $this->render('@CeradTourn/results/sportsmanship/index.html.twig', $tplData);
    }
}
?>
