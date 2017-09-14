<?php

namespace AppBundle\Form;

use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


/**
 * Class AdminContactType
 */
class OrgManagersContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization',
                TypeaheadType::class,
                array(
                    'label' => 'Organization',
                    'source_name' => 'organizations',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'starts_with', // ends_with, contains
                    'source' => $options['data']['organizations'],
                    'required' => 'true',
                    'invalid_message' => 'There is not any organization with this name',
                )
            )
            ->add(
                'orgManagersTitle',
                TextType::class,
                array(
                    "label" => "Title",
                    "attr" => array(

                    ),
                    "required" => false,
                )
            )
            ->add(
                'orgManagersMessage',
                TextareaType::class,
                array(
                    "label" => "Message",
                    "attr" => array(

                    ),
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
