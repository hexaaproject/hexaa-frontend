<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class AdminAttributeSpecType
 */
class AdminAttributeSpecType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'attributeSpecName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'attributeSpecDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'attributeSpecURI',
                TextType::class,
                array(
                    "label" => "OID",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'attributeSpecMaintainer',
                ChoiceType::class,
                array(
                    "label" => "Maintainer",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('user' => 'user', 'manager' => 'manager'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'attributeSpecSyntax',
                ChoiceType::class,
                array(
                    "label" => "Syntax",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('string' => 'string', 'base64' => 'base64'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'attributeSpecIsMultivalue',
                ChoiceType::class,
                array(
                    "label" => "Multivalue",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('true' => true, 'false' => false),
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
