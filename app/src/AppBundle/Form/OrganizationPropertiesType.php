<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;

/**
 * Class OrganizationPropertiesType
 *
 * @package AppBundle\Form
 */
class OrganizationPropertiesType extends AbstractType
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
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['name'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['description'],
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'url',
                TextType::class,
                array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['url'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => false,
                )
            )
            ->add(
                'default_role_id',
                TypeaheadType::class,
                array(
                    'label' => 'Default role',
                    'label_attr' => array('class' => 'formlabel'),

                    'data' => $datas['data']['properties']['default_role_id'],
                    'source_name' => 'roles',
                    'source' => $datas['data']['properties']['roles'],
                    'label_key' => 'name',
                    'value_key' => 'id',

                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'required' => 'false',
                    'attr' => array('class' => 'modified_twitter pull-right'),
                    'limit' => 7,
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
