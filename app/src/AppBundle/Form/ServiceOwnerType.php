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

/**
 * Class ServiceOwnerType
 * @package AppBundle\Form
 */
class ServiceOwnerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'serviceOwnerName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerName'],
                    'attr' => array('class' => 'col-sm-11'),
                    'required' => false,
                )
            )
            ->add(
                'serviceOwnerShortName',
                TextType::class,
                array(
                    "label" => "Short name",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerShortName'],
                    'attr' => array('class' => 'col-sm-11'),
                    'required' => false,
                )
            )
            ->add(
                'serviceOwnerDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'col-sm-3 formlabelpropertiesfordescription'),
                    'data' => $datas['data']['properties']['serviceOwnerDescription'],
                    'attr' => array('class' => 'col-sm-11', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'serviceOwnerURL',
                TextType::class,
                array(
                    "label" => "Home page",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'data' => $datas['data']['properties']['serviceOwnerURL'],
                    'attr' => array('class' => 'col-sm-11'),
                    'required' => false,
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
