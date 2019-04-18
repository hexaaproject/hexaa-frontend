<?php
/**
 * Copyright 2016-2018 MTA SZTAKI ugyeletes@sztaki.hu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
                    "label_attr" => array('class' => 'col-sm-3 formlabelproperties'),
                    'data' => $datas['data']['properties']['serviceName'],
                    'attr' => array('class' => 'col-sm-11'),
                    'required' => true,
                )
            )
            ->add(
                'serviceDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'col-sm-3 formlabelpropertiesfordescription'),
                    'data' => $datas['data']['properties']['serviceDescription'],
                    'attr' => array('class' => 'col-sm-11', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'serviceURL',
                TextType::class,
                array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'col-sm-3 formlabelproperties'),
                    'data' => $datas['data']['properties']['serviceURL'],
                    'attr' => array('class' => 'col-sm-11'),
                    'required' => false,
                )
            )
            ->add(
                'serviceSAML',
                TypeaheadType::class,
                array(
                    'label' => 'SAML SP Entity ID',
                    'data' => $datas['data']['properties']['serviceSAML'],
                    'label_attr' => array('class' => 'col-sm-3 formlabelproperties'),
                    'source_name' => 'saml',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'contains', // ends_with, contains
                    'source' => $datas['data']['properties']['serviceEntityIDs'],
                    'required' => 'true',
                    'attr' => array('class' => 'col-sm-11 modified_twitter'),
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
