<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrganizationUserInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'message',
                TextareaType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Message"
                        ),
                    "constraints" => new Constraints\NotBlank()
                    )
                )
            ->add(
                'landing_url',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Landing url"
                        ),
                    "constraints" => new Constraints\Url()
                    )
                )
            ->add(
                'limit',
                IntegerType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Limit"
                        )
                    )
                )

            ->add(
                'locale',
                ChoiceType::class,
                array(
                    "label" => false,
                    'choices' => array(
                        'English' => "en",
                        'Magyar' => "hu"
                        )
                    )
                )
            ->add(
                'start_date',
                DateType::class,
                array(
                    'label' => 'Start of accept period',
                    'input' => 'string'
                    )
                )
            ->add(
                'end_date',
                DateType::class,
                array(
                    'label' => 'End of accept period',
                    'input' => 'string'
                    )
                )
            ->add(
                'emails',
                TextareaType::class,
                array(
                    "label" => "Send in emails",
                    "attr" => array(
                        "placeholder" => "E-mail addresses"
                    )
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
