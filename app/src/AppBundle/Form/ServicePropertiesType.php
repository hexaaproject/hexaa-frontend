<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;

class ServicePropertiesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'serviceName', TextType::class, array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceName'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => true
                )
            )
            ->add(
                'serviceDescription', TextareaType::class, array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceDescription'],
                    'attr' => array('class' => 'pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false
                )
            )
            ->add(
                'serviceURL', TextType::class, array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceURL'],
                    'attr' => array('class' => 'pull-right'),
                    'required' => false
                )
            )
            ->add(
                'serviceSAML', ChoiceType::class, array(
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
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }

}
