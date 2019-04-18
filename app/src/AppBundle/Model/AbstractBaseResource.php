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

use AppBundle\Exception\BackendException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class AbstractBaseResource
 * @package AppBundle\Model
*/
abstract class AbstractBaseResource
{
    protected $pathName;
    /** @var  Client */
    protected $client;
    protected $token;
    protected $tokenStorage;
    private $cachedResources = [];

    /**
     * BaseResource constructor.
     * @param Client       $client
     * @param TokenStorage $tokenStorage
    */
    public function __construct(Client $client, TokenStorage $tokenStorage)
    {
        $this->client = $client;
        $this->hexaaAdmin = "false";
        $this->tokenStorage = $tokenStorage;
        if ($tokenStorage->getToken()) {
            $user = $tokenStorage->getToken()->getUser();
            $this->token = $user->getToken();
        }
    }

    /**
     * GET token
     * @return token
    */
    public function getToken()
    {
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $this->token = $user->getToken();
        }

        return $this->token;
    }


    /**
     * GET collection of resource
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
    */
    public function cget(string $hexaaAdmin, string $verbose = "normal", int $offset = 0, int $pageSize = 1000): array
    {
        return $this->getCollection($this->pathName, $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET api properties
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
    */
    public function apget(string $hexaaAdmin, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection("properties", $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET a single resource in array format
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of resource to GET
     * @param string $verbose    One of minimal, normal or expanded
     * @return array
    */
    public function get(string $hexaaAdmin, string $id, string $verbose = "normal"): array
    {
	$key = implode('_', array($hexaaAdmin, $id, $verbose));
	if (! array_key_exists($key, $this->cachedResources)){
	    $this->cachedResources[$key] = $this->getSingular($this->pathName.'/'.$id, $hexaaAdmin, $verbose);
	}

	return $this->cachedResources[$key];
    }

    /**
     * PUT resource
     * Note: any fields of the resource left out of the call will be set to null.
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of resource to PUT
     * @param array  $data       data to PUT
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function put(string $hexaaAdmin, string $id, array $data): ResponseInterface
    {
        return $this->putCall($this->pathName.'/'.$id, $data, $hexaaAdmin);
    }

    /**
     * PATCH resource
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of resource to PATCH
     * @param array  $data       data to PATCH
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function patch(string $hexaaAdmin, string $id, array $data): ResponseInterface
    {
        return $this->patchCall($this->pathName.'/'.$id, $data, $hexaaAdmin);
    }

    /**
    * PATCH resource
    * @param string $id   ID of resource to PATCH
    * @param array  $data data to PATCH
    * @return \Psr\Http\Message\ResponseInterface
    */
    public function patchAdmin(string $id, array $data): ResponseInterface
    {
        return $this->patchCallAdmin($this->pathName.'/'.$id, $data);
    }

    /**
     * POST resource
     *
     * @param string $hexaaAdmin Admin hat
     * @param array  $data       data to POST
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function post(string $hexaaAdmin, array $data): ResponseInterface
    {
        return $this->postCall($this->pathName, $data, $hexaaAdmin);
    }

    /**
     * DELETE resource
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of resource to GET
     * @return array
    */
    public function delete(string $hexaaAdmin, string $id)
    {
        return $this->deleteCall($this->pathName.'/'.$id, $hexaaAdmin);
    }

    /**
     * DELETE resource
     *
     * @param string $id ID of resource to GET
     * @return array
    */
    public function deleteAdmin(string $id)
    {
        return $this->deleteCallAdmin($this->pathName.'/'.$id);
    }

    /**
     * @return array
     * @throws \Exception
    */
    public function getHeaders(): array
    {
        if ($this->getToken()) {
            $config = $this->client->getConfig();
            $headers = $config["headers"];
            $headers['X-HEXAA-AUTH'] = $this->token;
        } else {
            throw new \Exception('No token');
        }

        return $headers;
    }

    /**
     * @return mixed
    */
    public function getEntityIds()
    {
        $response = $this->client->get(
            'entityids',
            [
                'headers' => $this->getHeaders(),
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $path
     * @param string $admin
     * @param string $verbose
     * @param int $offset
     * @param int $pageSize
     * @param string $tags
     * @return array|null
    */
    protected function getCollection(string $path, string $admin = "false", string $verbose = "normal", int $offset = 0, int $pageSize = 25, array $tags = [])
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
                    'admin' => $admin,
                    'tags' => $tags,
                ),
            )
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $path
     * @param string $admin
     * @param string $verbose
     * @param int  $offset
     * @param int  $pageSize
     * @return array
    */
    protected function getCollectionAdmin(string $path, string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
                    'admin' => $admin,
                ),
            )
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $path
     * @param string $hexaaAdmin Admin hat
     * @param string $verbose
     * @return array
    */
    protected function getSingular(string $path, string $hexaaAdmin, string $verbose = 'normal'): array
    {
        $response = $this->client->get(
            $path,
            array(
                'query' => array('verbose' => $verbose, 'admin' => $hexaaAdmin),
                'headers' => $this->getHeaders(),
                'allow_redirects' => false,
            )
        );

        $retarray = json_decode($response->getBody(), true);
        if (! $retarray) {
            $retarray = array();
        }

        return $retarray;
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $hexaaAdmin Admin hat
     * @return ResponseInterface
     *
     * @throws BackendException
    */
    protected function patchCall(string $path, array $data, string $hexaaAdmin): ResponseInterface
    {
        try {
            $response = $this->client->patch(
                $path,
                [
                    'json'    => $data,
                    'headers' => $this->getHeaders(),
                    'query' => array(
                      'admin' => $hexaaAdmin,
                    ),
                ]
            );
        } catch (RequestException $exception) {
            throw new BackendException($exception->getMessage());
        }

        return $response;
    }

    /**
    * @param string $path
    * @param array  $data
    * @param string $admin
    * @return ResponseInterface
    * @throws BackendException
    */
    protected function patchCallAdmin(string $path, array $data, string $admin = "true"): ResponseInterface
    {
        if ($admin == "1") {
            $admin = "true";
        }
        try {
            $response = $this->client->patch(
                $path,
                [
                    'json'    => $data,
                    'headers' => $this->getHeaders(),
                    'query' => array(
                        'admin' => $admin,
                    ),
                ]
            );
        } catch (RequestException $exception) {
            throw new BackendException($exception->getMessage());
        }

        return $response;
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $hexaaAdmin Admin hat
     * @return ResponseInterface
    */
    protected function putCall(string $path, array $data, string $hexaaAdmin): ResponseInterface
    {
        try {
            $response = $this->client->put(
                $path,
                [
                    'json' => $data,
                    'headers' => $this->getHeaders(),
                    'query' => array(
                      'admin' => $hexaaAdmin,
                    ),
                ]
            );
        } catch (RequestException $exception) {
            throw new BackendException($exception->getMessage());
        }


        return $response;
    }

    /**
     * @param string $path
     * @param array  $data
     * @param string $admin
     * @return ResponseInterface
    */
    protected function putCallAdmin(string $path, array $data, string $admin = "true"): ResponseInterface
    {
        if ($admin == "1") {
            $admin = "true";
        }

        $response = $this->client->put(
            $path,
            [
                'json' => $data,
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $admin,
                ),
            ]
        );

        return $response;
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $hexaaAdmin Admin hat
     * @return ResponseInterface
    */
    protected function postCall(string $path, array $data, string $hexaaAdmin): ResponseInterface
    {
        try {
            $response = $this->client->post(
                $path,
                [
                    'json' => $data,
                    'headers' => $this->getHeaders(),
                    'query' => array(
                      'admin' => $hexaaAdmin,
                    ),
                ]
            );
        } catch (RequestException $exception) {
            throw new BackendException($exception->getMessage());
        }

        return $response;
    }

    /**
     * @param string $path
     * @param array  $data
     * @param string $admin
     * @return ResponseInterface
    */
    protected function postCallAdmin(string $path, array $data, string $admin = "true"): ResponseInterface
    {
        if ($admin == "1") {
            $admin = "true";
        }
        $response = $this->client->post(
            $path,
            [
                'json' => $data,
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $admin,
                ),
            ]
        );

        return $response;
    }

    /**
     * @param string $path
     * @param array $data
     * @param string $hexaaAdmin Admin hat
     * @return ResponseInterface
    */
    protected function deleteCall(string $path, string $hexaaAdmin): ResponseInterface
    {
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

    /**
     * @param string $path
     * @param array $data
     * @return ResponseInterface
    */
    protected function deleteCallAdmin(string $path): ResponseInterface
    {
        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => "true",
                ),

            ]
        );

        return $response;
    }
}
