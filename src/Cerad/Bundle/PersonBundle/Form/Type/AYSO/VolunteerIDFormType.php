<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Cerad\Bundle\PersonBundle\DataTransformer\AYSO\VolunteerIDTransformer as Transformer;

class VolunteerIDFormType extends AbstractType
{
    public function getName()   { return 'cerad_person_ayso_volunteer_id'; }
    public function getParent() { return 'text'; }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Do the double transformer to handle errors
        $transformer = new Transformer();
        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer ($transformer);
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'AYSO Volunteer ID (8-digits)',
            'attr'  => array('size' => 10),
        ));
    }
}

?>
