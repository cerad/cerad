<?php
namespace Cerad\Bundle\TournAdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MainController extends Controller
{
    protected function isAdmin()
    {
        return $this->container->get('security.context')->isGranted('ROLE_ADMIN'); 
    }
    public function indexAction(Request $request)
    {
        if (!$this->isAdmin()) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
            
        $tplData = array();
        return $this->render('@CeradTournAdmin/index.html.twig', $tplData);
    }
    public function refereeExportAction()
    {
        if (!$this->isAdmin()) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
       
        $export = $this->get('cerad_tourn_admin.referee.export');
        $response = new Response($export->generate());
                 
        $outFileName = 'Referees' . date('YmdHi') . '.xls';
        
        $response->headers->set('Content-Type',       'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
        
        return $response;
    }
}
?>
