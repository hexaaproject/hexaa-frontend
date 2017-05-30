<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrganizationType
 */
class OrganizationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Szervezet neve",
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
                        "placeholder" => "Szervezet leírása",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'role',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Alapértelmezett szerepkör",
                    ),
                    "required" => true,
                )
            )
            ->add(
                'service_token',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Szolgáltatás token-je",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'invitation_emails',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Meghívottak email címei",
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
