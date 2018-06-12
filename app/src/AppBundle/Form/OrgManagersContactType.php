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

use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class AdminContactType
 */
class OrgManagersContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'organization',
                TypeaheadType::class,
                array(
                    'label' => 'Organization',
                    'source_name' => 'organizations',
                    'min_length' => 1,
                    'placeholder' => 'Start typing',
                    'matcher' => 'starts_with', // ends_with, contains
                    'source' => $options['data']['organizations'],
                    'required' => 'true',
                    'invalid_message' => 'There is not any organization with this name',
                )
            )
            ->add(
                'orgManagersTitle',
                TextType::class,
                array(
                    "label" => "Title",
                    "required" => false,
                )
            )
            ->add(
                'orgManagersMessage',
                TextareaType::class,
                array(
                    "label" => "Message",
                    "required" => false,
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
