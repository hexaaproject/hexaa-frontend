<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
class OrganizationRoleUpdateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $membersChoices = array();
        foreach ($datas['data']['principals'] as $principal) {
            $membersChoices[$principal['principal']['display_name'].' <'.$principal['principal']['fedid'].'>'] = $principal['principal']['fedid'];
        }

        $permissionChoices =  array(
            "test" => "Test",
            "test1" => "Test2",
            "test2" => "Test3",
        );


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
                'permissions',
                ChoiceType::class,
                array(
                    "label" => "Permissions",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array(),
                    'required' => true,
                    "choices" => $permissionChoices,
                    "multiple" => true,
                )
            )
            ->add(
                'members',
                ChoiceType::class,
                array(
                    "label" => "Members",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array(),
                    'required' => true,
                    "choices" => $membersChoices,
                    "multiple" => true,
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
