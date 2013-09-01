<?php
namespace Cerad\Bundle\TournBundle\Controller\Person;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\PersonBundle\Validator\Constraints\AYSO\VolunteerId as FedIdConstraint;

use Symfony\Component\Validator\Constraints\NotBlank as NotBlankConstraint;

class EditController extends Controller
{
    protected function createEditForm($person)
    {
        // I just invented dto's
        $dto = array();
        $dto['person'] = $person;  // Want to reuse basic form and validation
        
        $personIdentifier = $person->getIdentifierAYSOV();
        $cert = $personIdentifier->getCertReferee();
        $org  = $personIdentifier->getOrgRegion(); // Should default but doesn't hurt
        
        $dto['fedId'] = $personIdentifier->getId(); // TODO: Deal with the impossible case of being blank
        $dto['orgId'] = $org->getOrgId();
        $dto['badge'] = $cert->getBadge();
        
        $personType = $this->get('cerad_tourn.person_edit.form_type');
        $fedIdType  = $this->get('cerad_person.ayso_volunteer_id.form_type');
        $orgIdType  = $this->get('cerad_person.ayso_region_id.form_type');
        $badgeType  = $this->get('cerad_person.ayso_referee_badge.form_type');
        
        $formOptions = array(
            'validation_groups'  => array('update'),
            'cascade_validation' => true,
        );
        $constraintOptions = array('groups' => 'update');
        
        $builder = $this->createFormBuilder($dto, $formOptions);
        
        // Validated with @CeradAccount/validation.yml
        $builder->add('person', $personType);
        
        $builder->add('fedId',$fedIdType, array(
            'constraints' => array(
                new NotBlankConstraint($constraintOptions), 
                new FedIdConstraint   ($constraintOptions),
            ),
            'disabled' => true,
        ));
        $builder->add('orgId',$orgIdType, array(
            'constraints' => array(
                new NotBlankConstraint($constraintOptions), 
              //new FedIdConstraint   ($constraintOptions)),
        )));
        $builder->add('badge',$badgeType, array(
            'constraints' => array(
              //new NotBlankConstraint($constraintOptions), 
              //new FedIdConstraint   ($constraintOptions)),
        )));
        
        return $builder->getForm();
    }
    public function editAction(Request $request, $id = null)
    {   
        // Possible person id
        $personId = $id;
        
        // Make sure sign in
        $account = $this->getUser();
        if (!is_object($account)) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        $accountPersonId = $account->getPersonId();
          
        // It's possible that an account was created but no person
        if (!$personId) $personId = $accountPersonId;
        if (!$personId) return $this->redirect($this->generateUrl('cerad_person_create'));
        
        // Load the person
        $personRepo = $this->container->get('cerad_person.repository');
        $person = $personRepo->find($personId);
        if (!$person) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        // Should probably verify have proper access
        if ($personId != $accountPersonId)
        {
            // Verify under PersonPerson
        }
        
        // Form stuff
        $form = $this->createEditForm($person);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            $dto = $form->getData();
            $person = $dto['person'];
            $orgId  = $dto['orgId'];
            $badge  = $dto['badge'];
            
            $personIdentifier = $person->getIdentifierAYSOV();
            $personOrg  = $personIdentifier->getOrgRegion();
            $personCert = $personIdentifier->getCertReferee();
            
            $personOrg->setOrgId  ($orgId);
            $personCert->setBadgex($badge);
        
            $personRepo->persist($person);
            $personRepo->flush();
            
            return $this->redirect($this->generateUrl('cerad_person_edit'));
        }
        
        $tplData = array();
        $tplData['form']   = $form->createView();
        $tplData['person'] = $person;
        return $this->render('@CeradTourn/Person/Edit/index.html.twig', $tplData);
    }
}
?>
