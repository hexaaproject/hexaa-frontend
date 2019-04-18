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
use Devmachine\Bundle\FormBundle\Form\Type\TypeaheadType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class ConnectServiceRequest2Type
 */
class ConnectServiceRequest2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $datas
     */
    public function buildForm(FormBuilderInterface $builder, array $datas)
    {
        $builder
            ->add(
                'entitlementpacks',
                ChoiceType::class,
                array(
                    'label' => "Entitlement packs",
                  //'choices' => $datas['data']['datas']['entitlementsToForm'],
                    "attr" => array(
                        "class" => "checkstolink",
                    ),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                )
            )
            ->add(
                'entitlements',
                ChoiceType::class,
                array(
                    'label' => "Entitlements",
                  //'choices' => $datas['data']['datas']['entitlementsToForm'],
                    "attr" => array(
                        "class" => "checkstolink",
                    ),
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
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
