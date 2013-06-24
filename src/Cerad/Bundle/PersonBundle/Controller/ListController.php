<?php
namespace Cerad\Bundle\PersonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/* ===============================================
 * Should this be in the admin bundle?
 * Person
 *     PersonLeague
 *         League
 */
class ListController extends Controller
{
    public function listAction(Request $request)
    {
        // New person
        $personManager = $this->get('cerad_person.manager');
        $persons = $personManager->findAll();
        
        // Try linking leagues
        // Want some sort of cerad_person.league.manager?
        if ($this->container->has('cerad_game.league.manager'))
        {
            $leagueManager = $this->container->get('cerad_game.league.manager');
            $leagueManager->connectLeagues($persons);
        }

        // And show
        $tplData = array();
        $tplData['persons' ] = $persons;
        return $this->render('@CeradPerson/list/index.html.twig', $tplData);
    }
}
?>
