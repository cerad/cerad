<?php
namespace Cerad\Bundle\PersonBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cerad\Bundle\PersonBundle\Entity\Person;
use Cerad\Bundle\PersonBundle\Entity\PersonCert;

class PersonController extends Controller
{
    public function aysoVolRegionAction(Request $request)
    {
        $item = array(
            'ayso_region_id'     => null, 
            'ayso_volunteer_id'  => null,
            'ayso_referee_badge' => null,
        );
        
        $data = $request->getSession()->get('ayso_volunteer_region');
        
        if ($data) $item = array_merge($item,$data);
        
        $formType = $this->get('cerad_person.ayso_volunteer_region.form_type');
        
        $form = $this->createForm($formType,$item);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $item = $form->getData(); // print_r($item); die( 'POSTED');
                
                $request->getSession()->set('ayso_volunteer_region',$item);
                
                return $this->redirect($this->generateUrl('cerad_person_ayso_volunteer_region'));
            }
        }
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        
        return $this->render('@person/AYSO/volunteer_region.html.twig',$tplData);
    }
    public function createAction(Request $request)
    {
        $manager = $this->get('cerad_person.manager');
        $person = $manager->newPerson();
        
        $formType = $this->get('cerad_person.person.form_type');
        
        $form = $this->createForm($formType,$person);
        
        $tplData = array();
        $tplData['form'] = $form;
        
        return $this->render('@schedule/index.html.twig',$tplData);
    }
  public function signupAction(Request $request, $project, $op = null)
    {
        $tourns = $this->getParameter('tourns');
        
        if (!isset($tourns[$project])) return $this->welcomeAction($request);
        
        $tourn = $tourns[$project];
        
        $manager = $this->get('cerad_tourn.tourn_official.manager');
        $manager->setTournMeta($tourn);
        
        $official = null;
        
        $session = $request->getSession();
        $msg = $session->getFlash('tournMessage');
        
        // Start over if a new operation
        if ($op == 'new')
        {
            $session->set('tournOfficialId',null);
            return $this->redirect($this->generateUrl('cerad_tourn', array('project' => $project)));
        }
        
        // Load existing from session
        $id = $session->get('tournOfficialId');
        if ($id && ($id > 50)) // From changing formats
        {
            $official = $manager->loadOfficialForId($id);
            if ($official)
            {
                // Make sure not in different project or something
                $ok = true;
                foreach(array('season','sport','group','groupSub') as $name)
                {
                    if ($official[$name] != $tourn[$name]) $ok = false;
                }
                if (!$ok) $official = null;
            }
            if ($official)
            {
                $person = $official->getPerson();
                $cert   = $person->getCertRefereeUSSF();
            }
        }
        // Make a new one
        if (!$official)
        {
            $official = new OfficialPlans($tourn['plan']);
            foreach(array('season','sport','group','groupSub') as $name)
            {
                $official[$name] = $tourn[$name];
            }
            $person = new Person();
            $person->setGender(Person::GenderMale);
            
            $cert = PersonCert::createRefereeUSSF();
            $cert->setBadgex(PersonCert::BadgeGrade8);
            
            $person->addPlan($official);
            $person->addCert($cert);
        }
        $item = array(
            'person' => $person, 
            'cert'   => $cert,
            'plans'  => $official,
        );
        
        $formType = $this->get('cerad_tourn.signup.formtype');
        
        $formFactory = $this->container->get('form.factory');
        
        $form = $formFactory->create($formType,$item);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid())
            {
                // Make badges match
                $cert->setBadge($cert->getBadgex());
                
                // Save it
                $manager->persist($official);
                $manager->persist($person);
                $manager->persist($cert);
                
                $manager->flush();
                
                // Email it
                $this->sendRefereeEmail($tourn,$official);
                
                // Tuck ID away in session
                $id = $official->getId();
                
                $session->set     ('tournOfficialId',$id);
                $session->setFlash('tournMessage',   'Application Submitted');
                
                return $this->redirect($this->generateUrl('cerad_tourn',array('project' => $project)));
            }
            else $msg = 'Form not valid';
        }
        $tplData = $tourn;
        $tplData['msg']      = $msg;
        $tplData['form']     = $form->createView();
        $tplData['official'] = $official;
        return $this->render('CeradTournBundle:Tourn:signup.html.twig',$tplData);
    }
    protected function sendRefereeEmail($tourn,$plans)
    {   
        $prefix = $tourn['prefix']; // OpenCup2013
        
        $assignorName  = $tourn['assignor']['name'];
        $assignorEmail = $tourn['assignor']['email'];
        
      //$assignorEmail = 'ahundiak@nasoa.org';
        
        $adminName =  'Art Hundiak';
        $adminEmail = 'ahundiak@gmail.com';
        
        $refereeName  = $plans->getPerson()->getFirstName() . ' ' . $plans->getPerson()->getLastName();
        $refereeEmail = $plans->getPerson()->getEmail();
        
        $tplData = $tourn;
        $tplData['plans'] = $plans; 
        $body = $this->renderView('CeradTournBundle:Tourn:email.txt.twig',$tplData);
    
        $subject = sprintf("[%s] Ref App %s",$prefix,$refereeName);
       
        // This goes to the assignor
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom(array('admin@zayso.org' => $prefix));
        $message->setBcc (array($adminEmail => $adminName));
        
        $message->setTo     (array($assignorEmail  => $assignorName));
        $message->setReplyTo(array($refereeEmail   => $refereeName));

        $this->get('mailer')->send($message);
      //return;
        
        // This goes to the referee
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject);
        $message->setBody($body);
        $message->setFrom(array('admin@zayso.org' => $prefix));
      //$message->setBcc (array($adminEmail => $adminName));
        
        $message->setTo     (array($refereeEmail  => $refereeName));
        $message->setReplyTo(array($assignorEmail => $assignorName));

        $this->get('mailer')->send($message);
    }
}
?>
