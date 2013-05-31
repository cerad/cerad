<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ResultsController extends Controller
{
    public function poolplayAction(Request $request)
    {   
        $tplData = array();
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
}
?>
