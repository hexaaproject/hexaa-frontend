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
 * Date: 2017.03.13.
 * Time: 13:49
 */

namespace AppBundle\Model;

/**
 * Class EntitlementPack
 * @package AppBundle\Model
 */
class EntitlementPack extends AbstractBaseResource
{
    protected $pathName = 'entitlementpacks';

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getPublic(string $hexaaAdmin, string $verbose = 'normal', int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/public',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlementsOfEntitlementpack(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlements',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlementsDetails(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id,
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * Add permission to permissionset
     * @param string $hexaaAdmin Admin hat
     * @param string $id         of entitlementpack
     * @param string $permid     of entitlement
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function addPermissionToPermissionSet(string $hexaaAdmin, string $id, string $permid)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlements'.'/'.$permid, [], $hexaaAdmin);
    }

    /**
    * Sets permissions in permissionset
    * @param string $hexaaAdmin Admin hat
    * @param string $id         of entitlementpack
    * @param string $ids        of entitlements
    * @return \Psr\Http\Message\ResponseInterface
    */
    public function setPermissionsToPermissionSet(string $hexaaAdmin, string $id, array $ids)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlement', array("entitlements" => $ids), $hexaaAdmin);
    }

    /**
     *DELETE permission set
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of permission
     * @return response
     */
    public function deletePermissionSet(string $hexaaAdmin, string $id)
    {
        $path = $this->pathName.'/'.$id;

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $hexaaAdmin,
                ),
            ]
        );

        return $response;
    }
}
