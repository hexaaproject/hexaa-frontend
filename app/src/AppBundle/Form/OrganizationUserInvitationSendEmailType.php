<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class OrganizationUserInvitationSendEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'link',
                UrlType::class,
                array(
                    "label" => false,
                    "constraints" => new Constraints\Url(),
                    "attr" => array(
                        "class" => "col-md-10",
                        "readonly" => true
                    )
                )
            )
            ->add(
                'role_id',
                TextType::class,
                array(
                    "attr" => array(
                        "hidden" => true
                    )
                )
            )
            ->add(
                'message',
                TextareaType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Message"
                    )
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
                'emails',
                TextareaType::class,
                array(
                    "label" => "Send invitation by email",
                    "attr" => array(
                        "placeholder" => "Comma separated list of email addresses"
                    )
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
