<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Person;

//use Zayso\CoreBundle\Constraint\UserNameConstraint;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

/* ======================================================================
 * Lots and lots of forms, this one edits an ayso volunteer
 * Works on a person object
 */
class EditFormType extends AbstractType
{
    protected $manager = null;
    
    public function getName() { return 'cerad_tourn_person_edit'; }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('firstName', 'text', array('label' => 'AYSO First Name', 'constraints' => $notBlank));
        $builder->add('lastName',  'text', array('label' => 'AYSO Last Name',  'constraints' => $notBlank));
        $builder->add('nickName',  'text', array('label' => 'Nick Name',       'required' => false,));
        
        $builder->add('phone', 'cerad_person_phone',  array('required' => false,));
        $builder->add('email', 'cerad_person_email',  array('required' => true,));
        
        $builder->add('volunteerAYSO',   new AYSO\VolunteerFormType());
        $builder->add('certRefereeAYSO', new AYSO\RefereeFormType());
      
        // AYSO Stuff (labels etc can be overridden)
        //$builder->add('aysoVolunteerId', 'cerad_person_ayso_volunteer_id');
        //$builder->add('aysoRegionId',    'cerad_person_ayso_region_id');
        //$builder->add('aysoRefereeBadge','cerad_person_ayso_referee_badge');
    }
}