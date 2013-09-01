<?php
namespace Cerad\Bundle\TournBundle\Controller\Account;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cerad\Bundle\PersonBundle\Validator\Constraints\AYSO\VolunteerId as FedIdConstraint;
use Symfony\Component\Validator\Constraints\NotBlank as NotBlankConstraint;

class RegistrationController extends Controller
{
    /* =============================================
     * Want to be able to customize the default account registration form
     * 
     * Make this it's own method so as to allow overriding the default controller
     * 
     * For this to work, the Account:RegistrationController needs to be defined as a service
     * Try it later
     * 
     * onRegistrationInitialize
     */
    protected function createRegisterForm($user,$project = null)
    {
        // Add federation is tostandard user registration, pull type from project later
        $userType  = $this->get('cerad_account.create.formtype');
        $fedIdType = $this->get('cerad_person.ayso_volunteer_id.form_type');
        
        $formData = array(
            'user'      => $user,
            'fedIdType' => null,
        );
        $formOptions = array(
            'validation_groups'  => array('registration','create'),
            'cascade_validation' => true, // Not needed
        );
        $constraintOptions = array('groups' => 'registration');
        
        $builder = $this->createFormBuilder($formData, $formOptions);
        
        // Validated with @CeradAccount/validation.yml
        $builder->add('user', $userType);
        
        $builder->add('fedId',$fedIdType, array(
            'constraints' => array(
                new NotBlankConstraint($constraintOptions), 
                new FedIdConstraint   ($constraintOptions)),
        ));
                    
      //$builder->add('update', 'submit')

        return $builder->getForm();
    }
    /* =============================================
     * Abstract out the is valid form processing
     * Return a response to redirect
     */
    protected function onRegistrationSuccess($data)
    {
        /* ======================
         * The validated user
         */
        $user = $data['user'];
                
        /* =======================================
         * Might want to deal with the possibility that
         * A person for this is already exists
         * There needs to be some kind of security check, even if it's just an email
         */
        $fedId = $data['fedId'];
        
        $personRepo = $this->container->get('cerad_person.repository');
        $personIdentifier = $personRepo->findIdentifier($fedId);
        if ($personIdentifier)
        {
            $person = $personIdentifier->getPerson();
        }
        else
        {
            $person = $personRepo->newPerson();
            
            $person->setName ($user->getName());
            $person->setEmail($user->getEmail());
            
            $personIdentifier = $person->newIdentifier();
            
            $personIdentifier->setId  ($fedId);
            $personIdentifier->setRole('AYSOV');
            $person->addIdentifier($personIdentifier);
        }
        $user->setPersonId($person->getId());
        
        $personRepo->persist($person);
        $personRepo->flush();
        
        $userManager = $this->container->get('cerad_account.user_manager');
        $userManager->updateUser($user);
        
        return $this->redirect($this->generateUrl('cerad_person_edit'));
    }
    /* =============================================
     * /account/register ends up here
     * 
     * Dispatch the messages so Janrain can add additional user identifiers
     * 
     * As for a federated id to facilitate joining the account to a person
     */
    public function registerAction(Request $request)
    {
        // Grab a new user
        $userManager = $this->container->get('fos_user.user_manager');
        $dispatcher  = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);
        
        $event = new UserEvent($user, $request);
        
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE,$event);
        
        // Really should make my own event and add a janrain profile
        $source = isset($event->source) ? $event->source : null;
        
        // Build the customized form
        $form = $this->createRegisterForm($user);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            /* ==========================================
             * So initial validation passed
             * This is only supposed to modify the form data?
             * Also allows an optional response
             * 
             * Possible propmen in that my real user form is nested down one level
             */
            $formEvent = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $formEvent);

            // This updates the user as well as the person and flushes everything
            $response = $this->onRegistrationSuccess($form->getData());
            
            // See if success want to redirect us
            if (!$response) $response = $formEvent->getResponse();
            
            if (!$response) $response = $this->redirect($this->generateUrl('cerad_account_home'));
            
            // Everything is great, generate the correct redirect
            // And actually sign the user in?
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            return $response;
        }
        ///else die('invalid');
        
       // Render
        $tplData = array();
        $tplData['form'] = $form->createView();
        $tplData['source'] = $source;
        return $this->render('@CeradAccount/Registration/Register/index.html.twig', $tplData);        
    }
}
?>
