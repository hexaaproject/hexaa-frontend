<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

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
                        "placeholder" => "Üzenet"
                        )
                    )
                )
            ->add(
                'redirectUrl',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Átirányítási url"
                        )
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
                'roles',
                ChoiceType::class,
                array(
                    "label" => false,
                    'multiple' => true,
                    'choices' => $options['data']['roles']
                    )
                )
            ->add(
                'language',
                ChoiceType::class,
                array(
                    "label" => false,
                    'choices' => array(
                        'Magyar' => "hu",
                        'English' => "en"
                        )
                    )
                )
            ->add(
                'begin',
                DateType::class,
                array(
                    'label' => 'Érvényesség kezdete'
                    )
                )
            ->add(
                'end',
                DateType::class,
                array(
                    'label' => 'Érvényesség vége'
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
