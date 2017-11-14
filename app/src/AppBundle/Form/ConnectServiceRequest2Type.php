<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ConnectServiceRequest2Type
 */
class ConnectServiceRequest2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'entitlementpacks',
                ChoiceType::class,
                array(
                    'label' => "Entitlement packs",
                  //'choices' => $datas['data']['datas']['entitlementsToForm'],
                    "attr" => array(
                        "class" => "checkstolink",
                    ),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'entitlements',
                ChoiceType::class,
                array(
                    'label' => "Entitlements",
                  //'choices' => $datas['data']['datas']['entitlementsToForm'],
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
