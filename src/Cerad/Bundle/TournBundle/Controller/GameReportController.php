<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GameReportController extends Controller
{
    public function reportAction(Request $request, $num)
    {   
        // Verify have permissions
        
        // Verify have a game number
        if (!$num)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_home'));
        }
        
        // Need a better way to get the actual project object
        $projectManager = $this->get('cerad.project.repository');
        $projectParams  = $this->get('cerad_tourn.project');
        $projectEntity  = $projectManager->findOneBy(array('hash' => $projectParams->getKey()));
        if (!$projectEntity)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_home'));
        }
        
        // Grab the games
        $gameManager = $this->get('cerad.game.repository');
        
        $game = $gameManager->loadGameForProjectNum($projectEntity,$num);
        if (!$game)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_home'));
        }

        // Build the form
        $formType = $this->get('cerad_game.report.formtype');
        $form = $this->createForm($formType,$game);
        
        // New 2.3 style
        $form->handleRequest($request);
        
        // Check post
            if ($form->isValid())
            {
                // Cacl points
                $game = $form->getData();
                
                $resultsManager = $this->get('cerad_tourn.results.manager');
                $resultsManager->calcPointsEarnedForGame($game);
                
                $saved = $this->reportProcess($gameManager,$game);
                if ($saved)
                {
                    return $this->redirect($this->generateUrl('cerad_tourn_game_report',array('num' => $num)));
                }
            }
 
        // Render
        $tplData = array();
        $tplData['form'] = $form->createView();
        $tplData['game'] = $game;
        
        return $this->render('@CeradTourn/game/report.html.twig', $tplData);
    }
    protected function reportProcess($gameManager,$game)
    {
        // Bit of safety
        if (!$this->isUserScorer()) return false;
        
        // Bit of game status workflow
        $gameStatus = $game->getStatus();
        switch($gameStatus)
        {
            case 'Normal': 
            case 'InProgress':
                $game->setStatus('Played'); 
                break;
        }
        // Bit of game report workflow
        $reportStatus = $game->getReport()->getStatus();
        switch($reportStatus)
        {
            case 'Future':  
                return false;
                
            case 'Pending': 
                $game->getReport()->setStatus('Submitted'); 
                break;
            
            case 'Approved':
            case 'Verified':
                break;
        }
        // And persist, everything cascades
        $gameManager->flush();
        return true;
    }
    protected function isUserScorer()
    {
        return true;
    }
}
?>
