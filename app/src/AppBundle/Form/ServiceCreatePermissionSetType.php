<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ServiceCreatePermissionSetType
 *
 * @package AppBundle\Form
 */
class ServiceCreatePermissionSetType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'permissionSetName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'permissionSetDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'permissionSetType',
                ChoiceType::class,
                array(
                    "label" => "Type",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('private' => 'private', 'public' => 'public'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
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
