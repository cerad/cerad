<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Person\Person;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class AddFormType extends AbstractType
{
    protected $manager = null;
    
    public function getName() { return 'cerad_tourn_person_person_add'; }
    
    public function __construct($manager = null)
    {
        $this->manager = $manager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('personFirstName', 'text', array('label' => 'AYSO First Name', 'constraints' => $notBlank));
        $builder->add('personLastName',  'text', array('label' => 'AYSO Last Name',  'constraints' => $notBlank));
        $builder->add('personNickName',  'text', array('label' => 'Nick Name',       'required' => false,));
        
        $builder->add('personEmail', 'email'); // Not unique? Could be existing
        $builder->add('personPhone', 'cerad_person_phone',  array('required' => false,));
        
        // AYSO Stuff (labels etc can be overridden)
        $builder->add('aysoVolunteerId', 'cerad_person_ayso_volunteer_id');
        $builder->add('aysoRegionId',    'cerad_person_ayso_region_id');
        $builder->add('aysoRefereeBadge','cerad_person_ayso_referee_badge');
        
        // PersonPerson Role
        $builder->add('role', 'choice', array(
            'label'   => 'Role',
            'choices' => array
            (
                'Family' => 'Family',
                'Peer'   => 'Peer', 
            ),
        ));
    }
}