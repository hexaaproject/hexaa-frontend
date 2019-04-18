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

use AppBundle\Tools\Warning\MemberLessRoleWarning;
use AppBundle\Tools\Warning\PermissionLessRoleWarning;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Role
 * @package AppBundle\Model
 */
class Role extends AbstractBaseResource
{
    protected $pathName = 'roles';


    /**
     * GET entitlements of Role
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of role
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET principals of Role
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of role
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getPrincipals(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/principals', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * @param string $hexaaAdmin    Admin hat
     * @param string $id
     * @param string $entitlementId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putEntitlements(string $hexaaAdmin, string $id, string $entitlementId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlements/'.$entitlementId, [], $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin   Admin hat
     * @param string $id
     * @param array  $entitlements
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setEntitlements(string $hexaaAdmin, string $id, array $entitlements)
    {
        return $this->putCall($this->pathName.'/'.$id.'/entitlement', $entitlements, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin  Admin hat
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putPrincipal(string $hexaaAdmin, string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/principals/'.$principalId, [], $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin  Admin hat
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deletePrincipal(string $hexaaAdmin, string $id, string $principalId)
    {
        return $this->deleteCall($this->pathName.'/'.$id.'/principals/'.$principalId, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param array  $principals
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function setPrincipals(string $hexaaAdmin, string $id, array $principals)
    {
        if (empty($principals)) {
            $principals['principals'] = [];
        }
        return $this->putCall($this->pathName.'/'.$id.'/principal', $principals, $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     *
     * @return ArrayCollection
     */
    public function getWarnings(string $hexaaAdmin, $id)
    {
        $warnings = new ArrayCollection();

        $entitlements = $this->getEntitlements($hexaaAdmin, $id);
        if (0 == $entitlements['item_number']) {
            $role = $this->get($hexaaAdmin, $id);
            $warning = new PermissionLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        $principals = $this->getPrincipals($hexaaAdmin, $id);
        if (0 == $principals['item_number']) {
            $role = $this->get($hexaaAdmin, $id);
            $warning = new MemberLessRoleWarning($role['name']);
            $warnings->add($warning);
        }

        return $warnings;
    }
}
