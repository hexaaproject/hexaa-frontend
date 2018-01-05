<?php

namespace AppBundle\Form;

use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
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
                        "class" => "col-md-5 col-md-offset-5 createform",
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
                        "class" => "col-md-5 col-md-offset-5 createform ",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'url',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "URL of service",
                        "class" => "col-md-5 col-md-offset-5 createform ",
                    ),
                    "required" => false,
                    /*"constraints" => new Constraints\Url(),*/
                )
            )
           ->add(
               'entityid',
               TypeaheadType::class,
               array(
                   "label" => false,
                   'placeholder' => 'Start typing entity ID',
                   'source_name' => 'entityid',
                   'min_length' => 1,
                   'matcher' => 'contains',
                   'source' => $entityidsarray['data'],
                   "attr" => array(
                       "class" => "entityidtypeahead",
                       "id" => "scrollable-dropdown-menu",
                   ),
                   'required' => true,
                   'limit' => 30,

               )
           )
            ->add(
                'entitlement',
                TextType::class,
                array(
                    "label" => false,
                    'required' => true,
                    'data' => 'Default permission',
                    //       'placeholder' => 'Name of default permission',
                    'attr' => array(
                        'class' => "col-md-5 col-md-offset-5 createform ",
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
                        'class' => "col-md-5 col-md-offset-5 createform ",
                        'placeholder' => 'optional permission name 1',
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
                        'class' => "col-md-5 col-md-offset-5 createform",
                        'placeholder' => 'optional permission name 2',
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
