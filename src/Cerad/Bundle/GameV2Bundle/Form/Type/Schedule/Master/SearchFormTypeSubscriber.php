<?php
namespace Cerad\Bundle\GameV2Bundle\Form\Type\Schedule\Master;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchFormTypeSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $projectManager;
    
    public function __construct(FormFactoryInterface $factory, $projectManager)
    {
        $this->factory = $factory;
        $this->projectManager = $projectManager;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) return;
        
        $form = $event->getForm();
        
        // Get projects
        $projectManager = $this->projectManager;
        
        $projectSearchData = array();
        
        if (count($data['domains']))
        {
            $projectSearchData['domain'] = $data['domains'];
        }
        $projects = $projectManager->findBy($projectSearchData,array(
            'season'    => 'DESC',
            'domain'    => 'ASC',
            'domainSub' => 'ASC',
        ));
         
        // Make some choices
        $projectChoices   = array(0 => 'All Leagues/Tournaments/Regions');
        $sportChoices     = array(0 => 'All Sports');
        $sourceChoices    = array(0 => 'All Sources');
        $domainSubChoices = array(0 => 'All Sub Domains');
                
        foreach($projects as $project)
        {
            $projectChoices  [$project->getId()]        = $project->getName();
            $sportChoices    [$project->getSport()]     = $project->getSport();
            $sourceChoices   [$project->getSource()]    = $project->getSource();
            $domainSubChoices[$project->getDomainSub()] = $project->getDomainSub();    
        }
        // Generate project select
        $form->add($this->factory->createNamed('projects', 'choice', null, array(
            'label'           => 'Projects',
            'choices'         => $projectChoices,
            'required'        => false,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 10),
        )));
        // Generate season select
        $seasonChoices = $projectManager->findSeasonChoices();
        array_unshift($seasonChoices,'All Seasons');
        $form->add($this->factory->createNamed('seasons', 'choice', null, array(
            'label'           => 'Seasons',
            'required'        => false,
            'choices'         => $seasonChoices,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 4),
        )));
        // Generate domain select
        $domainChoices = $projectManager->findDomainChoices();
        array_unshift($domainChoices,'All Domains');
        $form->add($this->factory->createNamed('domains', 'choice', null, array(
            'label'           => 'Domains',
            'choices'         => $domainChoices,
            'required'        => false,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 4),
        )));
        
        // Generate field pick list
        $projectsSearch = $data['projects'];
        $fieldChoices = $this->projectManager->findFieldChoices($projectsSearch);
        
        $header = sprintf('All Fields(%d)',count($fieldChoices));
        array_unshift($fieldChoices,$header);
        
        $form->add($this->factory->createNamed('fields', 'choice', null, array(
            'label'           => 'Fields',
            'choices'         => $fieldChoices,
            'required'        => false,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 10),
        )));
        
        // Generate level pick list
        $projectsSearch = $data['projects'];
        $levelChoices = $this->projectManager->findLevelChoices($projectsSearch);
        
        $header = sprintf('All Levels(%d)',count($levelChoices));
        array_unshift($levelChoices,$header);
        
        $form->add($this->factory->createNamed('levels', 'choice', null, array(
            'label'           => 'Levels',
            'choices'         => $levelChoices,
            'required'        => false,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 10),
        )));
        
        // Generate team pick list
        $projectsSearch = $data['projects'];
        $teamChoices = $this->projectManager->findTeamChoices($projectsSearch);
        
        $header = sprintf('All Teams(%d)',count($teamChoices));
        array_unshift($teamChoices,$header);
        
        $form->add($this->factory->createNamed('teams', 'choice', null, array(
            'label'           => 'teams',
            'choices'         => $teamChoices,
            'required'        => false,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 10),
        )));
        return;
        
        // Generate sports pick list
        $names  = $this->manager->loadDomainSubChoices($data);
        array_unshift($names,'All Sub Groups');
        $form->add($this->factory->createNamed('domainSubs', 'choice', null, array(
            'label'         => 'Sub Groups',
            'required'      => false,
            'choices'       => $names,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
        // Generate groups pick list
        $names  = $this->manager->loadDomainChoices($data);
        array_unshift($names,'All Groups');
        $form->add($this->factory->createNamed('domains', 'choice', null, array(
            'label'         => 'Groups',
            'required'      => false,
            'choices'       => $names,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
        
        // Generate seasons pick list
        $names = $this->manager->loadSeasonChoices($data);
        array_unshift($names,'All Seasons');
        $form->add($this->factory->createNamed('seasons', 'choice', null, array(
            'label'         => 'Seasons',
            'required'      => false,
            'choices'       => $names,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 4),
        )));
        // Generate sports pick list
        $names = $this->manager->loadSportChoices($data);
        array_unshift($names,'All Sports');
        $form->add($this->factory->createNamed('sports', 'choice', null, array(
            'label'         => 'Sports',
            'required'      => false,
            'choices'       => $names,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 4),
        )));
    }
}
?>
