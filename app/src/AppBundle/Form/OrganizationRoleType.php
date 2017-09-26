<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;

/**
 * Class OrganizationRoleType
 *
 * @package AppBundle\Form
 */
class OrganizationRoleType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array(),
                    'required' => true,
                )
            )
            ->add(
                'wantToBeAMember',
                CheckboxType::class,
                array(
                    "label" => "do you want to be a member of the new role?",
                    "data" => true,
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
