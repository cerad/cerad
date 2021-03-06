<?php
/* ===============================================================
 * This is not currently used.  Instear the TournBundle is used.
 * However, eventually want the functionaity here with some customization if the
 * routing paths etc.
 */
namespace Cerad\Bundle\PersonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonPersonController extends Controller
{
    public function getAccountPerson()
    { 
        // Must have a person
        $account = $this->getUser();
        if (!is_object($account)) return null;
        
        $person = $account->getPerson();
        if (!is_object($person)) return null;

        return $person;
    }
    public function homeAction()
    {
        // Must have an account and person
        $person = $this->getAccountPerson();
        if (!$person)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_welcome'));  
        }
        // Also make sure have a project record
        $project = $this->get('cerad_tourn.project');
        $personPlan = $person->getPlan($project);
        
        if (!$personPlan)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_person_plan'));              
        }
       
        // Just display
        $tplData = array();
        $tplData['account'] = $this->getUser();
        $tplData['project'] = $this->get('cerad_tourn.project');
        
        return $this->render('@CeradTourn/home.html.twig', $tplData);
    }
    public function userHeaderAction()
    {
        $tplData = array();
        
        return $this->render('@project/user_header.html.twig', $tplData);
   }
   public function textAlertsAction()
    {
        $tplData = array();
        
        return $this->render('@project/text_alerts.html.twig', $tplData);
    }
    public function contactAction()
    {
        $tplData = array();
        
        return $this->render('@project/contact.html.twig', $tplData);
    }
    public function classesAction()
    {
        $tplData = array();
        return $this->render('@project/classes.html.twig', $tplData);
    }
    public function scheduleAction()
    {
        $manager = $this->get('cerad_legacy2012.game.manager');
        
        $games = $manager->loadGamesForDate('20120706');
        
        $tplData = array();
        $tplData['games'] = $games;
        
        return $this->render('@project/Schedule/schedule.html.twig', $tplData);
    }
    public function scheduleTeamAction()
    {
        $manager = $this->get('cerad_legacy2012.game.manager');
        
        $games = $manager->loadGamesForDate('20120706');
        
        $tplData = array();
        $tplData['games'] = $games;
        
        return $this->render('@project/Schedule/schedule.html.twig', $tplData);
    }
    public function scheduleRefereeAction()
    {
        $manager = $this->get('cerad_legacy2012.game.manager');
        
        $games = $manager->loadGamesForDate('20120706');
        
        $tplData = array();
        $tplData['games'] = $games;
        
        return $this->render('@project/Schedule/referee.html.twig', $tplData);
    }
    public function resultsAction()
    {
        $tplData = array();
        return $this->render('@project/Results/index.html.twig', $tplData);
    }
    protected function getProjectKey(Request $request, $projectKey = null)
    {
        if ($projectKey) return $projectKey;
        
        $projectKey = $request->attributes->get('projectKey');
        
        if ($projectKey) return $projectKey;
        
        $projectKey = $this->container->getParameter('cerad_zayso_project');
        
        return $projectKey;
       
    }
    public function volunteerPlanAction(Request $request, $projectKey = null)
    {
        $projectKey = $this->getProjectKey($request,$projectKey);
        
        // The form is called using an embedded ontroller
        $tplData = array();
        $tplData['projectKey'] = $projectKey;
        
      //$tplData['form'] = $form->createView();
        return $this->render('@project/Volunteer/plan.html.twig', $tplData);
    }
    public function volunteerPlanFormAction(Request $request, $projectKey = null)
    {
        $projectKey = $this->getProjectKey($request,$projectKey);
         
        // Setup the project manager
        $projects = $this->container->getParameter('cerad_zayso_projects');
        
        $projectManager = $this->container->get('cerad_zayso.project.manager'); 
        $projectManager->setProjectMetaData($projects[$projectKey]);
        
        // The form
        $volPlan = new VolPlan($projects[$projectKey]['plan']);
        $formType = $this->container->get('cerad_zayso.vol_plan.formtype');
        $form = $this->createForm($formType,$volPlan);
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@project/Volunteer/plan_form.html.twig', $tplData);
    }
    public function adminAction()
    {
        $tplData = array();
        
        return $this->render('@project/Admin/index.html.twig', $tplData);
    }
    public function searchAction(Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            $item = array('param1' => 'default1');
            $formType = $this->container->get('cerad_zayso.search.formtype');
            $form = $this->createForm($formType,$item);
            $form->bind($request);
            if ($form->isValid())
            {
                $item = $form->getData(); //print_r($item); die( 'POSTED');
                
                $request->getSession()->set('search',$item);
                
              //return $this->searchFormAction($request);                
                return $this->redirect($this->generateUrl('cerad_zayso_search'));      
            }
            else die('not valid');
        }
        $tplData = array();
        return $this->render('@project/search.html.twig', $tplData);
    }
    public function searchFormAction(Request $request)
    {
        $session = $request->getSession();
 
        $item = array('param1' => 'default1');
        if ($session->has('search'))
        {
            $item = array_merge($item,$session->get('search'));
        }
        $formType = $this->container->get('cerad_zayso.search.formtype');
        $form = $this->createForm($formType,$item);
         
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@project/search_form.html.twig', $tplData);
    }

}
?>
