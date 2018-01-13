<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrganizationUserChangeRolesType
 * @package AppBundle\Form
 */
class OrganizationUserChangeRolesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleChoices = array();
        if (array_key_exists("data", $options) && array_key_exists("organizationRoles", $options["data"])) {
            $roleChoices = $options["data"]["organizationRoles"];
        }
        $choices = array();
        foreach ($roleChoices as $roleChoice) {
            $choices[$roleChoice["name"]] = $roleChoice["id"];
        }

        $builder
            ->add(
                'roles',
                ChoiceType::class,
                array(
                    "label" => false,
                    "choices" => $choices,
                    "multiple" => true,
                    "expanded" => true,
                    "attr" => array(
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
