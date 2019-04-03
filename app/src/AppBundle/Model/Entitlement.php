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
 * Class Entitlement
 * @package AppBundle\Model
 */
class Entitlement extends AbstractBaseResource
{
    protected $pathName = 'entitlements';
    private $entitlementCache = [];

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param string $verbose
     * @param int    $offset
     * @param int    $pageSize
     * @return array
     */
    public function getEntitlement(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
	$key = implode('_', array($hexaaAdmin, $id, $verbose, $offset, $pageSize));
	if (! array_key_exists($key, $this->entitlementCache)){
          $this->entitlementCache[$key] = $this->getCollection(
            $this->pathName.'/'.$id,
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
          );
	}

	return $this->entitlementCache[$key];
    }

    /**
     *DELETE permission
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of permission
     * @return response
     */
    public function deletePermission(string $hexaaAdmin, string $id)
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
