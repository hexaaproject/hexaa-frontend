<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrganizationUserInvitationType
 */
class OrganizationUserInvitationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'limit',
                IntegerType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Limit",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'landing_url',
                UrlType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Landing url",
                    ),
                    "required" => false,
                    "constraints" => new Constraints\Url(),
                )
            )
            ->add(
                'start_date',
                DateType::class,
                array(
                    'label' => 'Start of accept period',
                    'input' => 'string',
                )
            )
            ->add(
                'end_date',
                DateType::class,
                array(
                    'label' => 'End of accept period',
                    'input' => 'string',
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
