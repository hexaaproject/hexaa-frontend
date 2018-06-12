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

/**
 * User: gyufi
 * Date: 2018. 03. 08.
 * Time: 8:49
 */

namespace AppBundle\Tools\Warning;

/**
 * Class OrphanPermissionSetWarning
 *
 * @package AppBundle\Tools\Warning
 */
class OrphanPermissionSetWarning extends Warning
{
    /**
     * @return string
     */
    public function getClass()
    {
        return "Orphan permission set";
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return "This permission set is not binded to any Organization.";
    }
}
