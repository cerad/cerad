<?php

/* ======================================================
 * 20 Jun 2013
 * This combines a person with their ayso region and regeree badge
 * 
 */
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class RefereeCreateFormType extends AbstractType
{
    public function getName()   { return 'cerad_person_ayso_referee_create'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Cerad\Bundle\PersonBundle\Entity\Person',
            'validation_groups'  => array('create','create_ayso'),
            'cascade_validation' => true,
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('name',      'text', array('label' => 'Full Name',       'constraints' => $notBlank));
        $builder->add('firstName', 'text', array('label' => 'AYSO First Name', 'constraints' => $notBlank));
        $builder->add('lastName',  'text', array('label' => 'AYSO Last Name',  'constraints' => $notBlank));
        $builder->add('nickName',  'text', array('label' => 'Nick Name',       'required' => false,));
        
        $builder->add('phone', 'cerad_person_phone',  array('required' => false,));
        $builder->add('email', 'cerad_person_email',  array('required' => true,));
        
        $builder->add('leagueAYSOVolunteer', 'cerad_person_ayso_volunteer');
        $builder->add('certAYSOReferee',     'cerad_person_ayso_referee_cert');
    }
}
?>
