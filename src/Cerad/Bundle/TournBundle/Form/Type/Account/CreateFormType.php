<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Account;

//use Zayso\CoreBundle\Constraint\UserNameConstraint;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class CreateFormType extends AbstractType
{
    protected $manager = null;
    
    public function getName() { return 'cerad_tourn_account_create'; }
    
    public function __construct($manager)
    {
        $this->manager = $manager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('userName', 'cerad_account_username');
        $builder->add('userPass', 'cerad_account_password');
        $builder->add('personEmail', 'cerad_account_email');
        /*
        $builder->add('userPass', 'repeated', array(
            'type'     => 'password',
            'label'    => 'Zayso Password',
            'required' => true,
            'invalid_message' => 'The password fields must match.',
            'constraints' => $notBlank,
            
            'first_options'  => array('label' => 'Zayso Password'),
            'second_options' => array('label' => 'Zayso Password(confirm)'),
            
            'first_name'  => 'pass1',
            'second_name' => 'pass2', // form.userPass.pass1
        ));*/
        $builder->add('personFirstName', 'text', array('label' => 'AYSO First Name', 'constraints' => $notBlank));
        $builder->add('personLastName',  'text', array('label' => 'AYSO Last Name',  'constraints' => $notBlank));
        $builder->add('personNickName',  'text', array('label' => 'Nick Name',       'required' => false,));
        
        $builder->add('personPhone', 'cerad_person_phone',  array('required' => false,));
        
        // AYSO Stuff (labels etc can be overridden)
        $builder->add('aysoVolunteerId', 'cerad_person_ayso_volunteer_id');
        $builder->add('aysoRegionId',    'cerad_person_ayso_region_id');
        $builder->add('aysoRefereeBadge','cerad_person_ayso_referee_badge');
    }
}