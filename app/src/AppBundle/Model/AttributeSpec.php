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

namespace AppBundle\Model;

/**
 * Class AttributeSpec
 * @package AppBundle\Model
 */
class AttributeSpec extends AbstractBaseResource
{
    protected $pathName = 'attributespecs';
    /**
     *GET attribute specifications
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    verbose
     * @param int    $offset     offset
     * @param int    $pageSize   pagesize
     * @return array
     */
    public function getAttributeSpec(string $hexaaAdmin, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName,
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     *Create attribute specification
     *
     * @param string $admin
     * @param array  $attributeSpec
     * @param string $verbose       verbose
     * @param int    $offset        offset
     * @param int    $pageSize      pagesize
     * @return response
     */
    public function createAttributeSpec(string $admin, array $attributeSpec, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        $response = $this->postCallAdmin($this->pathName, $attributeSpec, $admin);

        return $response;
    }

    /**
     * GET services linked to attribute value
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getServicesLinkedToAttributeSpec(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/services',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }
}
