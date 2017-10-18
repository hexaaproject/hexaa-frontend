<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ServiceCreateEmailType
 *
 * @package AppBundle\Form
 */
class ServiceCreateEmailType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'contactType',
                ChoiceType::class,
                array(
                    'label' => false,
                    /*'data' => $datas['data']['contacts'],*/
                    'choices' => $datas['data']['contacts'],
                    "attr" => array(
                        "class" => "contacts",
                    ),
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
