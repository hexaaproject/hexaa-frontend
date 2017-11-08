<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class ModifyConnectOrgType
 */
class ModifyConnectOrgType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
       //dump($datas);exit;
        $builder
            ->add(
                'entitlementpacks',
                ChoiceType::class,
                array(
                    'label' => "Entitlement packs",
                    'choices' => $datas['data']['entitlementpacksToForm'],
                    'data' => $datas['data']['currentEntitlementpacksToForm'],
                    "attr" => array(
                        "class" => "checkstolink",
                    ),
                    'required' => true,
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'link_id',
                HiddenType::class,
                array(
                    'data' => $datas['data']['linkid'],
                )
            )
            ->add(
                'entitlements',
                ChoiceType::class,
                array(
                    'label' => "Entitlements (optional)",
                    'choices' => $datas['data']['entitlementsToForm'],
                    'data' => $datas['data']['currentEntitlementsToForm'],
                    "attr" => array(
                        "class" => "checkstolink",
                    ),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
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
