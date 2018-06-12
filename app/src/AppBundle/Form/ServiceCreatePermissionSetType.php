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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ServiceCreatePermissionSetType
 *
 * @package AppBundle\Form
 */
class ServiceCreatePermissionSetType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
       /* $permissions =  array();
        if (array_key_exists('permissions', $datas['data'])) {
            foreach ($datas['data']['permissions'] as $permission) {
                $permissions[$permission]['name'] = $permission['id'];
            }
        }*/

        $builder
            ->add(
                'permissionSetName',
                TextType::class,
                array(
                    "label" => "Name",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'attr' => array('class' => 'col-sm-11 pull-right'),
                    'required' => true,
                )
            )
            ->add(
                'permissionSetDescription',
                TextareaType::class,
                array(
                    "label" => "Description",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'attr' => array('class' => 'col-sm-11 pull-right', 'cols' => '30', 'rows' => '1'),
                    'required' => false,
                )
            )
            ->add(
                'permissionSetType',
                ChoiceType::class,
                array(
                    "label" => "Type",
                    "label_attr" => array('class' => 'col-sm-3 formlabel'),
                    'choices' => array('private' => 'private', 'public' => 'public'),
                    'multiple' => false,
                    'expanded' => true,
                    'required' => true,
                )
            )
            ->add(
                'permissions',
                ChoiceType::class,
                array(
                    "label" => "Permissions",
                    "label_attr" => array('class' => 'formlabel'),
                    'attr' => array('data-role' => 'tagsinput'),
                    'required' => false,
                    "choices" => $datas['data']['permissions'],
                    "multiple" => true,
                )
            );

        $builder->get('permissions')->resetViewTransformers();
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
