<?php
namespace Cerad\Bundle\TournBundle\Controller\PersonPerson;

//  Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Controller\Person\EditController as BaseController;

/* ===============================
 * Older code, try using @CeradPerson controller?
 * Still want to customize for adding ayso folks
 */
class EditController extends BaseController
{
    /* ================================================
     * Extract the person person and verify security
     */
    public function findPersonPerson($personPersonId)
    {
        // Always need an id
        if (!$personPersonId) return null;
        
        // Make sure sign in
        $user = $this->getUser();
        if (!is_object($user)) return null;
        
        // Load the person mostly for security
        $personRepo = $this->container->get('cerad_person.repository');
        $userPerson = $personRepo->find($user->getPersonId());
        if (!$userPerson) return null;
        
        // Load it up
        $personPerson = $personRepo->findPersonPerson($personPersonId);
        if (!$personPerson) return null;
        
        // Check security
        
        // Got it
        return $personPerson;
        
    }
    public function editAction(Request $request, $id)
    {
        // Always required for now
        $personPersonId = $id;
        
        // Grab the object
        $personPerson = $this->findPersonPerson($personPersonId);
        if (!$personPerson) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));

        // For now, only process the person data
        $person = $personPerson->getSlave();
        
        // Form stuff
        $form = $this->createEditForm($person);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $dto = $form->getData();
            
            $this->processDto($dto);
                        
            return $this->redirect($this->generateUrl('cerad_person_person_edit',array('id' => $personPersonId)));
        }
        $tplData = array();
        $tplData['form']         = $form->createView();
        $tplData['person']       = $person;
        $tplData['personPerson'] = $personPerson;
        return $this->render('@CeradTourn/PersonPerson/Edit/index.html.twig', $tplData);
        
    }
}

?>
