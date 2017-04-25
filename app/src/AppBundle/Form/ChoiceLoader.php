<?php
/*
 * Copyright 2017 Annamari.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AppBundle\Form;

/**
 * Description of ChoiceLoader
 *
 * @author Annamari
 */

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;


class ChoiceLoader implements ChoiceLoaderInterface
{
    private $choices;
    
    function __construct(array $values){
        $this->choices = $values;
    }
   
    public function loadChoiceList($value = null)
    {
        return new ArrayChoiceList($this->choices);
    }

    public function loadChoicesForValues(array $values, $value = null)
    {
        $result = [ ];

        foreach ($values as $val)
        {
            $key = array_search($val, $this->choices, true);

            if ($key !== false)
                $result[ ] = $val;
        }

        return $result;
    }

    public function loadValuesForChoices(array $choices, $value = null)
    {
        $result = [ ];

        foreach ($choices as $choice)
        {
            $key = array_search($choice, $this->choices, true);

            if ($key !== false)
                $result[ ] = $choice;
        }

        return $result;
    }
}
