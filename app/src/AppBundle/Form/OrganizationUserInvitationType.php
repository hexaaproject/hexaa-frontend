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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
