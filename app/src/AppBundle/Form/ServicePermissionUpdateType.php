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
 * Class ServicePermissionUpdateType
 *
 * @package AppBundle\Form
 */
class ServicePermissionUpdateType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $urichunked = explode(':', $datas['data']['uri']);
        $uripostfix = $urichunked[5];
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'attr' => array(),
                    'required' => true,
                )
            )
            ->add(
                'description',
                TextType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'permissioneditlabel'),
                    'attr' => array(),
                    'required' => false,
                )
            )
            ->add(
                'uripost',
                TextType::class,
                array(
                    "label" => "URI",
                    "label_attr" => array('class' => 'permissionediturilabel'),
                    'attr' => array('class' => 'uripostfixstyle'),
                    'data' => $uripostfix,
                    'required' => true,
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
