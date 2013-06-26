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
            'required'        => false,
            'choices'         => $projectChoices,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
            'attr' => array('size' => 10),
        )));
        // Generate season select
        $seasonChoices = $projectManager->loadSeasonChoices();
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
        $domainChoices = $projectManager->loadDomainChoices();
        array_unshift($domainChoices,'All Domains');
        $form->add($this->factory->createNamed('domains', 'choice', null, array(
            'label'           => 'Domains',
            'required'        => false,
            'choices'         => $domainChoices,
            'expanded'        => false,
            'multiple'        => true,
            'disabled'        => false,
            'auto_initialize' => false,
          //'empty_data'      => null,
          //'empty_value'     => 'Help me',
            'attr' => array('size' => 4),
        )));
        return;
        
        // Generate field pick list
        $fields = $this->manager->loadFieldChoices($data);
        array_unshift($fields,'All Fields');
        $form->add($this->factory->createNamed('fields', 'choice', null, array(
            'label'         => 'Fields',
            'required'      => false,
            'choices'       => $fields,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
        return;
        
        // Generate field pick list
        $fields = $this->manager->loadFieldChoices($data);
        array_unshift($fields,'All Fields');
        $form->add($this->factory->createNamed('fields', 'choice', null, array(
            'label'         => 'Fields',
            'required'      => false,
            'choices'       => $fields,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
        // Generate team pick list
        $teams = $this->manager->loadTeamNames($data);
        array_unshift($teams,'All Teams');
        $form->add($this->factory->createNamed('teams', 'choice', null, array(
            'label'         => 'Teams',
            'required'      => false,
            'choices'       => $teams,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
        // Generate levels pick list
        $levels  = $this->manager->loadLevelChoices($data);
        array_unshift($levels,'All Levels');
        $form->add($this->factory->createNamed('levels', 'choice', null, array(
            'label'         => 'Levels',
            'required'      => false,
            'choices'       => $levels,
            'expanded'      => false,
            'multiple'      => true,
            'disabled'      => false,
            'attr' => array('size' => 10),
        )));
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
