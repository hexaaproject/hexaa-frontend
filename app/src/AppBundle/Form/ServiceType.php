<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class ServiceType
 */
class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $entityidsarray
     */
    public function buildForm(FormBuilderInterface $builder, array $entityidsarray)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Name of service",
                        "class" => "col-md-5 col-md-offset-5",
                    ),
                    "required" => true,
                )
            )
            ->add(
                'description',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Description of service",
                        "class" => "col-md-5 col-md-offset-5",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'url',
                UrlType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "URL of service",
                        "class" => "col-md-5 col-md-offset-5",
                    ),
                    "required" => false,
                    "constraints" => new Constraints\Url(),
                )
            )
            ->add(
                'entityid',
                ChoiceType::class,
                array(
                    "label" => false,
                    'choices' => $entityidsarray['data'],
                    'required' => true,
                    'choices_as_values' => true,
                    'attr' => array(
                        'class' => "col-md-5 col-md-offset-5",
                    ),
                    'choice_attr' => function ($key, $val, $index) {
                        if ($val == "Which entity id?") {
                            $disabled = true;
                        } else {
                            $disabled = false;
                        }

                        return $disabled ? ['disabled' => 'disabled'] : [];
                    },
                )
            )
            ->add(
                'entitlement',
                TextType::class,
                array(
                    "label" => false,
                    'required' => true,
                    'data' => 'default',
                    //       'placeholder' => 'Name of default permission',
                    'attr' => array(
                        'class' => "col-md-5 col-md-offset-5",
                    ),
                )
            )
            ->add(
                'entitlementplus1',
                TextType::class,
                array(
                    "label" => false,
                    'required' => false,
                    'attr' => array(
                        'class' => "col-md-5 col-md-offset-5",
                        'placeholder' => 'Name of plus permission',
                    ),
                )
            )
            ->add(
                'entitlementplus2',
                TextType::class,
                array(
                    "label" => false,
                    'required' => false,
                    'attr' => array(
                        'class' => "col-md-5 col-md-offset-5",
                        'placeholder' => 'Name of plus permission',
                    ),
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
