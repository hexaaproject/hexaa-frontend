<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrganizationUserMessageManagerType
 * @package AppBundle\Form
 */
class OrganizationUserMessageManagerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subject',
                TextType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Subject",
                    ),
                )
            )
            ->add(
                'message',
                TextareaType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Message",
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
