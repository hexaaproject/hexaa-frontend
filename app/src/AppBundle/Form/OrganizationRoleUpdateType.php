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
        if (array_key_exists('principals', $datas['data'])) {
            foreach ($datas['data']['principals'] as $principal) {
                $membersChoices[$principal['principal']['display_name'].' <'.$principal['principal']['fedid'].'>'] = $principal['principal']['fedid'];
            }
        }

        $entitlementChoices =  array();
        if (array_key_exists('entitlements', $datas['data'])) {
            foreach ($datas['data']['entitlements'] as $entitlement) {
                $entitlementChoices[$entitlement]['name'] = $entitlement['id'];
            }
        }

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
                'entitlements',
                ChoiceType::class,
                array(
                    "label" => "Permissions",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('data-role' => 'tagsinput'),
                    'required' => true,
                    "choices" => $entitlementChoices,
                    "multiple" => true,
                )
            )
            ->add(
                'members',
                ChoiceType::class,
                array(
                    "label" => "Members",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('data-role' => 'tagsinput'),
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
