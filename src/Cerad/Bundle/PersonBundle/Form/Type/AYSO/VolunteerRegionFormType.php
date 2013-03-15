<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 */
class VolunteerRegionFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ayso_volunteer_id', 'cerad_person_ayso_volunteer_id');
        $builder->add('ayso_region_id',    'cerad_person_ayso_region_id');
        $builder->add('ayso_referee_badge','cerad_person_ayso_referee_badge');
     }
    public function getName()   { return 'cerad_person_ayso_volunteer_person'; }
}

?>
