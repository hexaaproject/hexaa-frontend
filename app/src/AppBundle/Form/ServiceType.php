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
class ServiceType extends AbstractType
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
                        "placeholder" => "Name of service",
                        "class" => "col-md-5 col-md-offset-5"
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
                        "class" => "col-md-5 col-md-offset-5"
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
                        "class" => "col-md-5 col-md-offset-5"
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
