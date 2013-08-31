<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\USSF;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Cerad\Bundle\PersonBundle\DataTransformer\USSF\ContractorIDTransformer as Transformer;

class ContractorIDFormType extends AbstractType
{
    public function getName()   { return 'cerad_person_ussf_contractor_id'; }
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
            'label'       => 'USSF ID (16-digits)',
            'attr'  => array(
                'placeholder' => 'USSF ID (16-digits)',
                'size' => 20),
        ));
    }
}

?>
