<?php
namespace Cerad\Bundle\ZaysoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function signinAction(Request $request)
    {
        $tplData = array();
        return $this->render('@project/signin.html.twig',$tplData);        
    }
    public function signinFormAction(Request $request)
    {
        // Allow admin signin from this page
       $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) 
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } 
        else 
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove      (SecurityContext::AUTHENTICATION_ERROR);
        }
           
        $tplData = array();
        $tplData['signin_error']         = $error;
        $tplData['signin_csrf_token']    = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');
        $tplData['signin_last_username'] = $session->get(SecurityContext::LAST_USERNAME);
        
        return $this->render('@project/signin_form.html.twig',$tplData);
    }
}
?>
