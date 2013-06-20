<?php
namespace Cerad\Bundle\PersonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Cerad\Bundle\PersonBundle\DataTransformer\PhoneTransformer as Transformer;

class PhoneFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Note the use of both transformer
        $transformer = new Transformer();
        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer ($transformer);
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Cell Phone',
            'attr'  => array('size' => 20),
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_person_phone'; }
}

?>
