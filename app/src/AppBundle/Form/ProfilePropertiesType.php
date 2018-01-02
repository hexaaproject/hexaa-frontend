<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class ProfilePropertiesType
 * @package AppBundle\Form
 */
class ProfilePropertiesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'principalName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'col-sm-4 profileformlabel'),
                    'data' => $options['data']['properties']['principalName'],
                   /* 'attr' => array('class' => 'pull-right'),*/
                    'required' => true,
                )
            )
            ->add(
                'principalEmail',
                TextType::class,
                array(
                    "label" => "Email",
                    "label_attr" => array('class' => 'col-sm-4 profileformlabel'),
                    'data' => $options['data']['properties']['principalEmail'],
                   /* 'attr' => array('class' => 'pull-right'),*/
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
