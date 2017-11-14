<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ConnectServiceRequest1Type
 */
class ConnectServiceRequest1Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
      //  dump($datas);exit;
     //   $builder
    //        ->add(
               /* 'service',
                TypeaheadType::class,
                array(
                    'label' => 'Service',
                    'source_name' => 'services',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $datas['data'],
                    'required' => 'true',
                    'limit' => 30,
                )*/
      //      );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
