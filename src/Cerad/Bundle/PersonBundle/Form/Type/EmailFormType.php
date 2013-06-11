<?php
namespace Cerad\Bundle\PersonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Email;

class EmailFormType extends AbstractType
{
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_person_email'; }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Maybe a canilizier here
        // $builder->addModelTransformer(new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label'           => 'Email',
            'attr'            => array('size' => 30),
            'required'        => true,
            'constraints'     => array(
                new Email(array('message' => 'Invalid Email')), 
            )
        ));
    }
}

?>
