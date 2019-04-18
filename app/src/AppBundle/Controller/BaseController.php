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
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2017. 12. 12.
 * Time: 13:40
 */

namespace AppBundle\Controller;

use AppBundle\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BaseController
 *
 * @package AppBundle\Controller
 */
class BaseController extends Controller
{
    /**
     * @param $entity
     *
     * @return bool
     * @throws \Exception
     *
     *  TODO
     */
    protected function amIManagerOfThis($entity)
    {
        if (!$entity) {
            throw new Exception("TODO");
        }

        return true;
    }

    /**
     *
     * @return array
     * @throws \Exception
     *
     *  TODO
     */
    protected function orgWhereManager()
    {
        $hexaaAdmin = $this->get('session')->get('hexaaAdmin');
        if ($hexaaAdmin == null) {
            $hexaaAdmin = 'false';
        }

        return $this->get('principal')->orgsWhereUserIsManager($hexaaAdmin);
    }

    protected function getEntityShowPath($entity, $manager)
    {
        if ($this instanceof OrganizationController) {
            if ($manager == "true") {
                return $this->generateUrl("app_organization_show", ["id" => $entity["id"]]);
            } else {
                return $this->generateUrl("app_organization_properties", ["id" => $entity["id"]]);
            }
        }

        return "#";
    }
}
