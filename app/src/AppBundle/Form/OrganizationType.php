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
                        "placeholder" => "Name of organization",
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
                        "placeholder" => "Description of organization",
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
                        "placeholder" => "Name of default role",
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
                        "placeholder" => "Token of service",
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
                        "placeholder" => "Invitation email addresses",
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
