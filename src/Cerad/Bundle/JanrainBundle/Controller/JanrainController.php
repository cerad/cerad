<?php
namespace Cerad\Bundle\JanrainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

/* ==============================================
 * This will almost always be overriden by a user controller
 */
class JanrainController extends Controller
{
    public function loginAction(Request $request)
    {
      //$session = $request->getSession();

        $tplData = array();
        return $this->render('CeradJanrainBundle:Janrain:login.html.twig',$tplData);
    }
    public function registerAction(Request $request)
    {
        $session = $request->getSession();
        $profile = $session->get('cerad_janrain_profile');
        
        print_r($profile);
        die('register');
    }
}

?>
