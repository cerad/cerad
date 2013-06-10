<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/* ====================================================================
 * It would be nice to have this logic tucked away here but 
 * Problem is integration, how to get the header information when this is part of
 * a Tourn app or a different app class altogether
 * 
 * This could be a base class which is then extended by the tournament class
 * Things like extended template name would then be a parameter
 * 
 */
class IdentifierController extends Controller
{
    public function addAction(Request $request)
    {
        $account = $this->getUser();
        if (!is_object($account) || 1)
        {
            $route = $this->container->getParameter('cerad_route_welcome');
            return $this->redirect($this->generateUrl($route));    
        }
        $tplData = array();
        $tplData['error']   = null;
        $tplData['account'] = account;
        return $this->render('@CeradAccount/identifier/add.html.twig', $tplData);
    }
}
?>
