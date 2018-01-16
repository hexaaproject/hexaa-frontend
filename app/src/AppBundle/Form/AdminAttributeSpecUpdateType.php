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
 * Class AdminAttributeSpecUpdateType
 */
class AdminAttributeSpecUpdateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $multivalue = $options['data']['is_multivalue'];

        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    //'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                   // 'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'uri',
                TextType::class,
                array(
                    "label" => "URI",
                    "label_attr" => array('class' => 'formlabel'),
                   // 'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'maintainer',
                ChoiceType::class,
                array(
                    "label" => "Maintainer",
                    //"data" => "user",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('user' => 'user', 'manager' => 'manager'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'syntax',
                ChoiceType::class,
                array(
                    "label" => "Syntax",
                    //"data" => "string",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('string' => 'string', 'base64' => 'base64'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'Multivalue',
                ChoiceType::class,
                array(
                    "label" => "Multivalue",
                    //"data" => true,
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => array('true' => true, 'false' => null),
                    'data' => $multivalue,
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
