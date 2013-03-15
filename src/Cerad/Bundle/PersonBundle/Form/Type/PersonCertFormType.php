<?php

namespace Cerad\Bundle\CoreBundle\Form\Type\Person;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonCertFormType extends AbstractType
{
    protected $manager = null;
    protected $role    = null;
    
    public function __construct($manager = null, $role = null)
    {
        $this->manager = $manager;
        $this->role    = $role;
    }
    public function getName()   
    { 
        $name = 'cerad_core_person_cert'; 
        if ($this->role) $name .= '_' . $this->role;
        return $name;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\CoreBundle\Entity\PersonCert'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('idx', 'text', array(
            'label' => 'USSF ID(16 Digits)',
            'required' => false,
            'attr'     => array('size' => 20, 'placeholder' => '16 Digits'), 
         ));
        
        $builder->add('upgrading', 'choice', array(
            'label'         => 'Working on Upgrade',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => array('No' => 'No', 'Yes' => 'Yes'),
        ));
        $builder->add('badgex', 'choice', array(
            'label'         => 'User Badge',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => $this->badgeOptions,
        ));
        $builder->add('regOrg', 'choice', array(
            'label'         => 'State Certified In',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => $this->stateOptions,
        ));
        $builder->add('experience', 'integer', array(
            'label'         => 'Experience(years)',
            'required'      => false,
            'attr'          => array('size' => 6, 'placeholder' => 'years'), 
        ));
        
        // Admin only fields
        if ($this->role != 'admin') return;
        
         $builder->add('dateFirstCertified', 'cerad_core_date_or_null', array(
            'label'    => 'Date First Certified',
            'attr'     => array('size' => 12, 'placeholder' => 'YYYYMMDD'), 
            'required' => false,
        ));
        
        $builder->add('dateLastUpgraded', 'cerad_core_date_or_null', array(
            'label'    => 'Date Last Upgraded',
            'attr'     => array('size' => 12, 'placeholder' => 'YYYYMMDD'), 
            'required' => false,
        ));
       
        $builder->add('status', 'choice', array(
            'label'         => 'Status',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => array('Checking' => 'Checking', 'Active' => 'Active', 'InActive' => 'InActive'),
       ));
       
       $builder->add('verified', 'choice', array(
            'label'         => 'Verified',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => array('No' => 'No', 'Yes' => 'Yes'),
       ));
       
       $builder->add('badge', 'choice', array(
            'label'         => 'Real Badge',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => $this->badgeOptions,
        ));
         
        $builder->add('regYear', 'choice', array(
            'label'         => 'Last Year Registered',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => $this->regYearOptions,
        ));
       
    }
    protected $badgeOptions = array
    (
        'None'    => 'None',
        'Grade 9' => 'Grade 9',
        'Grade 8' => 'Grade 8',
        'Grade 7' => 'Grade 7',
        'Grade 6' => 'Grade 6',
        'Grade 5' => 'Grade 5',
        'Grade 4' => 'Grade 4',
    );
    protected $regYearOptions = array
    (
        'None' => 'None',
        '2013' => 'CY2013',
        '2014' => 'CY2014',
        '2012' => 'CY2012',
        '2011' => 'CY2011',
        '2010' => 'CY2010',
    );
    protected $stateOptions= array
    (
        'AL' => 'Alabama',
        'AR' => 'Arkansas',
        'GA' => 'Gerogia',
        'LA' => 'Louisiana',
        'MS' => 'Mississippi',
        'TN' => 'Tennessee',
        'ZZ' => 'See Notes',
    );
}
?>
