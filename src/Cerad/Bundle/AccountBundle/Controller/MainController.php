<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('cerad_account_welcome'));
    }
    public function welcomeAction(Request $request)
    {
        // Like to have login info here
        $authInfo = $this->get('cerad_account.authentication_information');
        $info = $authInfo->get($request);
        
        $item = array(
            'error'    => $info['error'],
            'username' => $info['lastUsername'], // Probably not really necessary
            'password' => null,
        );
        $loginForm = $this->createForm($this->get('cerad_account.signin.formtype'),        $item);
        
        $tplData = array();
        $tplData['loginForm'] = $loginForm->createView();
        return $this->render('@CeradAccount/welcome.html.twig', $tplData);        
    }
    protected function isAdmin()
    {
        return $this->container->get('security.context')->isGranted('ROLE_ADMIN'); 
    }
    protected function isUser()
    {
        return $this->container->get('security.context')->isGranted('ROLE_USER'); 
    }
    protected function redirectToWelcome()
    {
        return $this->redirect($this->generateUrl('cerad_account_welcome'));
    }
    public function homeAction(Request $request)
    {
        if (!$this->isUser()) return $this->redirectToWelcome();
        
        $tplData = array();
        return $this->render('@CeradAccount/home.html.twig', $tplData);        
    }
}
?>
