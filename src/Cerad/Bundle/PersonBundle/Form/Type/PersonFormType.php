<?php
namespace Cerad\Bundle\PersonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class PersonFormType extends AbstractType
{
    protected $manager;
    protected $role;
    
    public function __construct($manager = null, $role = null)
    {
        $this->manager = $manager;
        $this->role    = $role;
    }
    public function getName()   
    { 
        $name = 'cerad_person'; 
        if ($this->role) $name .= '_' . $this->role;
        return $name;
    }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\PersonBundle\Entity\Person'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('firstName', 'text', array('label' => 'First Name *', 'constraints' => $notBlank));
        $builder->add('lastName',  'text', array('label' => 'Last Name *',  'constraints' => $notBlank));
        
        // The class ends up on the input element
        $builder->add('nickName',  'text', array('label' => 'Nick Name',  'required' => false, 'attr' => array('class' => 'person_name')));

        $builder->add('email',     'email', array('label' => 'Email *',      'attr' => array('size' => 35)));
        
        $builder->add('phone', 'phone', array('label' => 'Cell Phone', 'attr' => array('size' => 20), 'required' => false,));
        
        $builder->add('dob', 'cerad_core_date_or_null', array(
            'label'    => 'Date of Birth(YYYYMMDD)',
          //'widget'   => 'single_text',
          //'format'   => 'yyyyMMdd',
            'attr'     => array('size' => 12, 'placeholder' => 'YYYYMMDD'), 
            'required' => false,
        ));
      //$builder->get('dob')->addModelTransformer(new DateOrNullTransformer());

      //$builder->add('age','text', array('label' => 'Your Age (years)','attr' => array('size' => 4)));
        
        $builder->add('gender', 'choice', array(
            'label'         => 'Your Gender',
            'required'      => false,
            'choices'       => $this->genderPickList,
            'expanded'      => true,
            'multiple'      => false,
            'attr' => array('class' => 'radio-medium'),
        ));
        
        $builder->add('state', 'choice', array(
            'label'         => 'Home State',
            'required'      => false,
            'empty_value'   => false,
            'choices'       => $this->statePickList,
        ));
        $builder->add('city', 'text', array(
            'label'    => 'Home City', 
            'required' => false, 
            'attr'     => array('size' => 30)));

        // Read only works for the text fields but not the select fields
        // Disabled works as expected though cannot copy/paste
        // $builder->add('regAYSOV', 'zayso_core_ayso_vol', array('read_only' => false, 'disabled' => false));

 
    }
    protected $genderPickList = array ('M' => 'Male', 'F' => 'Female');
    protected $statePickList = array
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
