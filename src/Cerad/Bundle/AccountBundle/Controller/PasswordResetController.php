<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use FOS\UserBundle\FOSUserEvents;

// FOS\UserBundle\Event\FormEvent;
// FOS\UserBundle\Event\GetResponseUserEvent;
// FOS\UserBundle\Event\UserEvent;

use FOS\UserBundle\Event\FilterUserResponseEvent;

use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PasswordResetController extends Controller
{
    const SESSION_DATA = 'cerad_account_password_reset';

    public function requestAction(Request $request)
    {
        // Majic to get any previous errors
        $authInfo = $this->get('cerad_account.authentication_information');
        $info = $authInfo->get($request);
        
        $item = array(
            'error'    => null,
            'username' => $info['lastUsername'],
        );
        $form = $this->createForm($this->get('cerad_account.password_reset.formtype'),$item);
        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            // Suppose a data transformer could be used here?
            $item = $form->getData();
            $username = $item['username'];
            
            $userProvider = $this->get('cerad_account.user_provider');
            $user = $userProvider->findUserByUsername($username);
            if (!$user)
            {
                // TODO: add error to form
                $item['error'] = 'User not found.';
            }
            else
            {
                // Should check to see how long since last request
                
                // Set the confirmation token
                if (!$user->getConfirmationToken() || 1) 
                {
                    $tokenGenerator = $this->container->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                }
                // My short tokens
                $token = mt_rand(1000,9999);
                
                // Tuck away email address for check email page
                $sessionData = array
                (
                    'id'    => $user->getId(),
                    'token' => $token,
                    'tries' => 0,
                );
                $request->getSession()->set(static::SESSION_DATA,$sessionData);
                
                // Send the actual email
                $this->sendEmail($user,$token);
                
                // Persist the updated user
                $user->setPasswordRequestedAt(new \DateTime());
                $this->container->get('cerad_account.user_manager')->updateUser($user);

                return $this->redirect($this->generateUrl('cerad_account_password_reset_confirm'));
            }
        }
        // Render
        $tplData = array();
        $tplData['passwordResetError'] = $item['error'];
        $tplData['passwordResetForm' ] = $form->createView();
        return $this->render('@CeradAccount/Password/Reset/request.html.twig', $tplData);
    }
    /* =============================================================
     * Not sure it really makes sense to abstract this
     * Lots of things to inject
     */
    protected function sendEmail(UserInterface $user,$token)
    {
        $fromName =  'Zayso Password Reset';
        $fromEmail = 'noreply@zayso.org';
        
        $adminName =  'Art Hundiak';
        $adminEmail = 'ahundiak@gmail.com';
       
        $userName  = $user->getName();
        $userEmail = $user->getEmail();
        
        $tplData = array();
        $tplData['user']   = $user;
        $tplData['token']  = $token;
        $tplData['prefix'] = 'Zayso';
        
        $body    = $this->renderView('@CeradAccount/Password/Reset/email_body.html.twig',  $tplData);
        $subject = $this->renderView('@CeradAccount/Password/Reset/email_subject.txt.twig',$tplData);

         // This goes to the assignor
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom(array($fromEmail  => $fromName ));
        $message->setBcc (array($adminEmail => $adminName));
        $message->setTo  (array($userEmail  => $userName ));

        $this->get('mailer')->send($message);
       
    }
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }
    /* =============================================================
     * Presents the confirmation token form and new password
     * Then updates everything if all is well
     */
    public function confirmAction(Request $request, $tokenx = null)
    {
        // The original very long token
        if ($tokenx) return $this->tokenAction($request,$tokenx);
        
        $error = null;
        
        $session     = $request->getSession();
        $sessionData = $session->get(static::SESSION_DATA);
        
        $tries = isset($sessionData['tries']) ? $sessionData['tries'] : null;
        $token = isset($sessionData['token']) ? $sessionData['token'] : null;
        
        // Load up the user which also checls the session data
        $userId      = isset($sessionData['id']) ? $sessionData['id'] : null;
        $userManager = $this->get('cerad_account.user_manager');
        $user        = $userManager->findUserBy(array('id' => $userId));
        
        if ((null === $user) || (null === $token) || (3 < $tries )) 
        {
            $session->remove(static::SESSION_DATA);
            return $this->redirect($this->generateUrl('cerad_account_welcome'));
        }
        // Form stuff
        $item = array(
            'token'    => null,
            'password' => null,
        );
        $form = $this->createFormBuilder($item)
            ->add('token',    'cerad_account_token')
            ->add('password', 'cerad_account_password')
          //->add('save',     'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            $item = $form->getData();
            if ($token == $item['token'])
            {
                $password = $item['password'];
                $user->setPlainPassword($password);
                $user->setConfirmationToken  (null);
                $user->setPasswordRequestedAt(null);
                $userManager->updateUser($user);
                
                $session->remove(static::SESSION_DATA);
                
                // Could goto to confirmationed screen
                $response = $this->redirect($this->generateUrl('cerad_account_home'));
                
                // Sign the user in
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                
                return $response;
            }
            // Maybe should generate a new token or at least add a counter
            $tries++;
            $sessionData['tries'] = $tries;
            $session->set(static::SESSION_DATA,$sessionData);
            
            $error = 'Invalid confirmation number entered, try again.';
            
        }
        $tplData = array();
        $tplData['form']  = $form->createView();
        $tplData['user']  = $user;
        $tplData['email'] = $this->getObfuscatedEmail($user);
        
        $tplData['error'] = $error;
        $tplData['tries'] = $tries;
        $tplData['token'] = $token;
        
        return $this->render('@CeradAccount/Password/Reset/confirm.html.twig', $tplData);
    } 
    /* =============================================================
     * This process the older style in which a long token is passed
     * as part of the reset process
     * TODO: Make the implementation
     */
    public function tokenAction(Request $request, $token = null)
    {
        $session     = $request->getSession();
        $sessionData = $session->get(static::SESSION_DATA);
        $confirmed   = false;
        $user        = null;
        if (is_array($sessionData))
        {
            $confirmed   = $sessionData['confirmed'];
            $userManager = $this->get('cerad_account.user_manager');
            $user        = $userManager->findUserBy(array('id' => $sessionData['id']));
        }
        if (!$confirmed || !$user)
        {
            // Should not get here
            $session->remove(static::SESSION_DATA);
            return $this->redirect($this->generateUrl('cerad_account_welcome'));
        }
        // The form
        $password = null;
        $form = $this->createForm($this->get('cerad_account.password.formtype'),$password);
        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            $password = $form->getData();
            // Need this for now
            if ($password)
            {
                $user->setPlainPassword($password);
                $user->setConfirmationToken  (null);
                $user->setPasswordRequestedAt(null);
                $userManager->updateUser($user);
                
                $session->remove(static::SESSION_DATA);
                
                $response = $this->redirect($this->generateUrl('cerad_account_home'));
                
                // Sign the user in
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                
                return $response;
            }
        }
        $tplData = array();
        $tplData['user'] = $user;
        $tplData['passwordChangeForm'] = $form->createView();
        return $this->render('@CeradAccount/Password/Reset/change.html.twig', $tplData);
        
    }
}
?>
