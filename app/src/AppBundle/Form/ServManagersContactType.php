<?php

namespace AppBundle\Form;

use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ManagersContactType
 */
class ServManagersContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'service',
                TypeaheadType::class,
                array(
                    'label' => 'Service',
                    'source_name' => 'services',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $options['data']['services'],
                    'required' => 'true',
                    'limit' => 30,
                )
            )
            ->add(
                'managersTitle',
                TextType::class,
                array(
                    "label" => "Title",
                    "required" => false,
                )
            )
            ->add(
                'managersMessage',
                TextareaType::class,
                array(
                    "label" => "Message",
                    "required" => false,
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
