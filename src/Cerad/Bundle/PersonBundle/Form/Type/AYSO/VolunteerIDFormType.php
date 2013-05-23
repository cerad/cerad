<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Cerad\Bundle\PersonBundle\DataTransformer\AYSO\VolunteerIDTransformer;

class VolunteerIDFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Do the double transformer to handle errors
        $builder->addModelTransformer(new VolunteerIDTransformer());
        $builder->addViewTransformer (new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          //'invalid_message' => 'Invalid id',
            'label'           => 'AYSO Volunteer ID (8-digits)',
            'attr'            => array('size' => 10),
            'constraints'     => array(
                new Assert\NotNull(array('message' => 'ID is required')), 
                new Assert\Regex  (array('message' => 'Invalid ID', 'pattern' => '/^(AYSOV)?\d{8}$/'))),
            
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_person_ayso_volunteer_id'; }
}

?>
