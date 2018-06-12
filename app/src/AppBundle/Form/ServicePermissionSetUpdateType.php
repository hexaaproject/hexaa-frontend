<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;

/**
 * Class ServicePermissionSetUpdateType
 *
 * @package AppBundle\Form
 */
class ServicePermissionSetUpdateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'attr' => array(),
                    'required' => true,
                )
            )
            ->add(
                'description',
                TextType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'attr' => array(),
                    'required' => false,
                )
            )
            ->add(
                'type',
                ChoiceType::class,
                array(
                    "label" => "Type",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'choices' => array('private' => 'private', 'public' => 'public'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'permissions',
                ChoiceType::class,
                array(
                    "label" => "Permissions",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'attr' => array('class' => 'permissionstotypeahead'),
                    'required' => false,
                    "choices" => $datas['data']['permissions'],
                    "multiple" => true,
                )
            );
        $builder->get('permissions')->resetViewTransformers();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
