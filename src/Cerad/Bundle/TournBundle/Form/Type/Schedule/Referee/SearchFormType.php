<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Schedule\Referee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchFormTypeSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $manager;
    private $params;
    
    public function __construct(FormFactoryInterface $factory, $manager, $params)
    {
        $this->factory = $factory;
        $this->manager = $manager;
        $this->params  = $params;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if ($data === null) return;
        
        $form = $event->getForm();
        
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
        $teams = $this->manager->loadTeamChoices($data);
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

class SearchFormType extends AbstractType
{
    public function getName() { return 'schedule_referee_search'; }
    
    protected $manager;
    protected $project;
    
    protected $params;

    public function __construct($manager,$project)
    {
        $this->manager = $manager;
        $this->project = $project;        
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For dynamic fields
        $subscriber = new SearchFormTypeSubscriber($builder->getFormFactory(),$this->manager,$this->params);
        $builder->addEventSubscriber($subscriber);

        $all = array('All' => 'All');
        
        // Dates
        $builder->add('dates', 'choice', array(
            'label'         => 'Days of Week',
            'required'      => true,
            'choices'       => array_merge($all,$this->project->getDates()), // Fri = label, 2013-06-14 = value
            'expanded'      => true,
            'multiple'      => true,
          //'attr' => array('class' => 'cerad-checkbox-all'), 
        ));
        $builder->add('ages', 'choice', array(
            'label'         => 'Ages',
            'required'      => true,
            'choices'       => array_merge($all,$this->project->getAges()),
            'expanded'      => true,
            'multiple'      => true,
          //'attr' => array('class' => 'cerad-checkbox-all'),
        ));
        $builder->add('genders', 'choice', array(
            'label'         => 'Genders',
            'required'      => true,
            'choices'       => array_merge($all,$this->project->getGenders()),
            'expanded'      => true,
            'multiple'      => true,
          //'attr' => array('class' => 'cerad-checkbox-all'),
        ));
        $builder->add('teamFilter',    'text', array('required' => false, 'attr' => array('size' => 30)));
        $builder->add('refereeFilter', 'text', array('required' => false, 'attr' => array('size' => 30)));
        
    }
}
?>
