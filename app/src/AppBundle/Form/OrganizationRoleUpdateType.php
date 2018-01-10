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
        $role = $datas["data"];

        $membersChoices = array();
        if (array_key_exists('organizationMembers', $role) && $role['organizationMembers']) {
            foreach ($role['organizationMembers'] as $organizationMember) {
                $membersChoices[$organizationMember['display_name'] . ' <' . $organizationMember['fedid'] . '>'] = $organizationMember['id'];
            }
        }

        $checkedMembersChoices = array();
        if (array_key_exists('principals', $datas['data'])) {
            foreach ($datas['data']['principals'] as $principal) {
                $checkedMembersChoices[] = $principal['principal']['id'];
            }
        }

        $entitlementChoices =  array();
        if (array_key_exists('organizationEntitlements', $role) && $role['organizationEntitlements']) {
            foreach ($role['organizationEntitlements'] as $entitlement) {
                $entitlementChoices[$entitlement['scoped_name']] = $entitlement['id'];
            }
        }
        $checkedEntitlementChoices = array();
        if (array_key_exists('entitlements', $datas['data'])) {
            foreach ($role['entitlements'] as $entitlement) {
                $checkedEntitlementChoices[] = $entitlement['id'] ;
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
//                    'attr' => array('data-role' => 'tagsinput'),
                    'required' => true,
                    "choices" => $entitlementChoices,
                    "data" => $checkedEntitlementChoices,
                    "multiple" => true,
                    "expanded" => true,
                )
            )
            ->add(
                'members',
                ChoiceType::class,
                array(
                    "label" => "Members",
                    "label_attr" => array('class' => 'formlabel'),
//                    'attr' => array('data-role' => 'tagsinput'),
                    'required' => true,
                    "choices" => $membersChoices,
                    "data" => $checkedMembersChoices,
                    "multiple" => true,
                    "expanded" => true,
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
