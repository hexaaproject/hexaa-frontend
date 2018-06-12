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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

/**
 * Class OrganizationUserInvitationType
 */
class OrganizationUserInvitationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'limit',
                IntegerType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Limit",
                    ),
                    "required" => false,
                )
            )
            ->add(
                'landing_url',
                UrlType::class,
                array(
                    "label" => false,
                    "attr" => array(
                        "placeholder" => "Landing url",
                    ),
                    "required" => false,
                    "constraints" => new Constraints\Url(),
                )
            )
            ->add(
                'start_date',
                DateType::class,
                array(
                    'label' => 'Start of accept period',
                    'input' => 'string',
                )
            )
            ->add(
                'end_date',
                DateType::class,
                array(
                    'label' => 'End of accept period',
                    'input' => 'string',
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
