<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ServicePrivacyType
 * @package AppBundle\Form
 */
class ServicePrivacyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'servicePrivacyURL',
                TextType::class,
                array(
                    "label" => "URL",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['servicePrivacyURL'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => false,
                )
            )
            ->add(
                'servicePrivacyDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['servicePrivacyDescription'],
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'disabled' => false,
        ));
    }
}
