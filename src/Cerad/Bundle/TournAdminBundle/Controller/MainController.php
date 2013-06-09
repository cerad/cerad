<?php
namespace Cerad\Bundle\TournAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class MainController extends Controller
{
    public function indexAction(Request $request)
    {
        // return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
            
        $tplData = array();
        return $this->render('@CeradTournAdmin/index.html.twig', $tplData);
    }
}
?>
