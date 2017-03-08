<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ServiceOwnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'serviceOwnerName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerName'],
                    'attr'  => array('class' => 'pull-right'),
                    'required' => false
                    )
                )
            ->add(
                'serviceOwnerShortName',
                TextType::class,
                array(
                    "label" => "Short name",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerShortName'],
                    'attr'  => array('class' => 'pull-right'),
                    'required' => false
                    )
                )
            ->add(
                'serviceOwnerDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerDescription'],
                    'attr'  => array('class' => 'pull-right', 'cols'=>'30', 'rows'=>'1'),
                    'required' => false
                    )
                )
            ->add(
                'serviceOwnerURL',
                TextType::class,
                array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerURL'],
                    'attr'  => array('class' => 'pull-right'),
                    'required' => false
                    )
                )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            
        ));
    }
}
