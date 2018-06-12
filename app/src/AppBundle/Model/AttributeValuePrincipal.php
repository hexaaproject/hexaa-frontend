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
 * User: solazs
 * Date: 2017.02.23.
 * Time: 15:45
 */

namespace AppBundle\Model;

/**
 * Class AttributeValuePrincipal
 * @package AppBundle\Model
 */
class AttributeValuePrincipal extends AbstractBaseResource
{
    protected $pathName = 'attributevalueprincipals';

    /**
     * GET services linked to attribute value
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getServicesLinkedToAttributeValue(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/services',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * Create attribute value for principal
     * @param string $hexaaAdmin  Admin hat
     * @param array  $services
     * @param string $value
     * @param int    $attrspecid
     * @param int    $principalid
     * @return ResponseInterface
     */
    public function postAttributeValue(string $hexaaAdmin, array $services, string $value, int $attrspecid, int $principalid)
    {
        $attributevalue = array();
        $attributevalue["value"] = $value;
        $attributevalue["services"] = $services;
        $attributevalue["attribute_spec"] = $attrspecid;
       // $attributevalue["principal"] = $principalid;
        $response = $this->postCall($this->pathName, $attributevalue, $hexaaAdmin);

        return $response;
    }
}
