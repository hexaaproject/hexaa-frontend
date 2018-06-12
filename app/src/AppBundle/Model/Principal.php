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

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Principal
 * @package AppBundle\Model
 */
class Principal extends AbstractBaseResource
{
    protected $pathName = 'principal';

    /**
     * GET the current Principal
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    One of minimal, normal or expanded
     * @param string $hexaatoken hexaa api token
     * @return array
     */
    public function getSelf(string $hexaaAdmin, string $verbose = "normal", $hexaatoken = null)
    {
        if ($hexaatoken) {
            $this->token = $hexaatoken;
        }

        return $this->getSingular($this->pathName.'/self', $hexaaAdmin, $verbose);
    }

    /**
     * GET info about Principal
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         Id of principal
     * @param string $verbose    One of minimal, normal or expanded
     * @return array
     */
    public function getPrincipalInfo(string $hexaaAdmin, string $id, string $verbose = "normal")
    {
        return $this->getSingular('principals'.'/'.$id.'/id', $hexaaAdmin, $verbose);
    }


    /**
     * GET attribute values of the current Principal
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getAttributeValues(string $hexaaAdmin, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/attributevalueprincipal', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET All principals
     *
     * @param string $admin
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAllPrincipals(string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollectionAdmin('principals', $admin, $verbose, $offset, $pageSize);
    }

    /**
     * Delete principal
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $admin
     * @param string $pid
     * @return array
     */
    public function deletePrincipal(string $hexaaAdmin, string $admin, string $pid)
    {
        if ($admin == "1" || $hexaaAdmin == "true") {
            $admin = "true";
        }
        $id = (int) ($pid);
        $path = 'principals/'.$id.'/id';

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $admin,
                ),
            ]
        );

        return $response;
    }

    /**
     * Edit principal properties
     *
     * @param string $hexaaAdmin
     * @param string $pid
     * @param array  $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editPrincipal(string $hexaaAdmin, string $pid, array $data)
    {
        $response = null;
        $id = (int) ($pid);
        $path = 'principals/'.$id;

       /* if ($admin == "1") {
            $admin = "true";
            $response = $this->putCallAdmin($path, $data, $admin);
        } else {
            $response = $this->putCall($path, $data);
        }*/

        $response = $this->patchCall($path, $data, $hexaaAdmin);

        return $response;
    }

    /**
     * Principal is admin or not?
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    One of minimal, normal or expanded
     * @return array
     */
    public function isAdmin(string $hexaaAdmin, string $verbose = "normal")
    {
        return $this->getSingular($this->pathName.'/isadmin', $hexaaAdmin, $verbose);
    }

    /**
     * Get the history of the principal
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getHistory(string $hexaaAdmin, string $verbose = "normal", int $offset = 0, int $pageSize = 500)
    {
        //$id = (int) ($pid);
        return $this->getCollection($this->pathName.'/news', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
    * List organizations where user is manager
    *
    * @param string $hexaaAdmin Admin hat
    * @param string $verbose    One of minimal, normal or expanded
    * @return array
    */
    public function orgsWhereUserIsManager(string $hexaaAdmin, string $verbose = "normal")
    {
        return $this->getSingular('manager/organizations', $hexaaAdmin, $verbose);
    }

    /**
    * List organizations where user is manager
    *
    * @param string $hexaaAdmin Admin hat
    * @param string $verbose    One of minimal, normal or expanded
    * @return array
    */
    public function servsWhereUserIsManager(string $hexaaAdmin, string $verbose = "normal")
    {
        return $this->getSingular('manager/services', $hexaaAdmin, $verbose);
    }

    /**
    * Get the entitlements of the principal
    * @param string $hexaaAdmin Admin hat
    * @param string $verbose
    * @return array
    */
    public function getEntitlements(string $hexaaAdmin, string $verbose = "normal")
    {
        return $this->getCollection($this->pathName.'/entitlements', $hexaaAdmin, $verbose);
    }
}
