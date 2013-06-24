<?php
namespace Cerad\Bundle\GameBundle\Controller\League;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ListController extends Controller
{
    public function listAction(Request $request)
    {
        $leagueManager = $this->get('cerad_game.league.manager');
        $leagues = $leagueManager->findAll();

        // And show
        $tplData = array();
        $tplData['leagues' ] = $leagues;
        return $this->render('@CeradGame/league/list/index.html.twig', $tplData);
    }
}
?>
