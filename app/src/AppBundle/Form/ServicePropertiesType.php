<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ServicePropertiesType
 *
 * @package AppBundle\Form
 */
class ServicePropertiesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'serviceName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabelproperties'),
                    'data' => $datas['data']['properties']['serviceName'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'serviceDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabelproperties'),
                    'data' => $datas['data']['properties']['serviceDescription'],
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'serviceURL',
                TextType::class,
                array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'formlabelproperties'),
                    'data' => $datas['data']['properties']['serviceURL'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => false,
                )
            )
            ->add(
                'serviceSAML',
                TypeaheadType::class,
                array(
                    'label' => 'SAML SP Entity ID',
                    'data' => $datas['data']['properties']['serviceSAML'],
                    'label_attr' => array('class' => 'entitylabel'),
                    'source_name' => 'saml',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $datas['data']['properties']['serviceEntityIDs'],
                    'required' => 'true',
                    'attr' => array('class' => 'modified_twitter pull-right'),
                    'limit' => 30,
                )
            );
          /*  ->add(
                'serviceSAML',
                ChoiceType::class,
                array(
                    "label" => "SAML SP Entity ID",
                    "label_attr" => array('class' => 'formlabel'),
                    'choices' => $datas['data']['properties']['serviceEntityIDs'],
                    'multiple' => false,
                    'attr' => array('class' => 'pull-right'),
                    'required' => true,
                    /*'choice_loader' => new CallbackChoiceLoader(function() {
                                return \AppBundle\Model\BaseResource::getEntityIds();
                            }
                    )*/
       /*         )
            );*/
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
