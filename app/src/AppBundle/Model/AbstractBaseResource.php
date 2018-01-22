<?php
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

    /**
     * BaseResource constructor.
     * @param Client       $client
     * @param TokenStorage $tokenStorage
    */
    public function __construct(Client $client, TokenStorage $tokenStorage)
    {
        $this->client = $client;
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
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
    */
    public function cget(string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName, $verbose, $offset, $pageSize);
    }

    /**
     * GET api properties
     *
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
    */
    public function apget(string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection("properties", $verbose, $offset, $pageSize);
    }

    /**
     * GET a single resource in array format
     *
     * @param string $id      ID of resource to GET
     * @param string $verbose One of minimal, normal or expanded
     * @return array
    */
    public function get(string $id, string $verbose = "normal"): array
    {
        return $this->getSingular($this->pathName.'/'.$id, $verbose);
    }

    /**
     * PUT resource
     * Note: any fields of the resource left out of the call will be set to null.
     *
     * @param string $id   ID of resource to PUT
     * @param array  $data data to PUT
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function put(string $id, array $data): ResponseInterface
    {
        return $this->putCall($this->pathName.'/'.$id, $data);
    }

    /**
     * PATCH resource
     *
     * @param string $id   ID of resource to PATCH
     * @param array  $data data to PATCH
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function patch(string $id, array $data): ResponseInterface
    {
        return $this->patchCall($this->pathName.'/'.$id, $data);
    }

    /**
    * PATCH resource
    *
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
     * @param array $data data to POST
     * @return \Psr\Http\Message\ResponseInterface
    */
    public function post(array $data): ResponseInterface
    {
        return $this->postCall($this->pathName, $data);
    }

    /**
     * DELETE resource
     *
     * @param string $id ID of resource to GET
     * @return array
    */
    public function delete(string $id)
    {
        return $this->deleteCall($this->pathName.'/'.$id);
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
     * @param string $verbose
     * @param int $offset
     * @param int $pageSize
     * @param string $tags
     * @return array|null
    */
    protected function getCollection(string $path, string $verbose = "normal", int $offset = 0, int $pageSize = 25, string $tags = null)
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
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
     * @param string $verbose
     * @return array
    */
    protected function getSingular(string $path, string $verbose = 'normal'): array
    {
        $response = $this->client->get(
            $path,
            array(
                'query' => array('verbose' => $verbose),
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
     * @return ResponseInterface
     *
     * @throws BackendException
    */
    protected function patchCall(string $path, array $data): ResponseInterface
    {
        try {
            $response = $this->client->patch(
                $path,
                [
                    'json'    => $data,
                    'headers' => $this->getHeaders(),
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
     * @return ResponseInterface
    */
    protected function putCall(string $path, array $data): ResponseInterface
    {
        try {
            $response = $this->client->put(
                $path,
                [
                    'json' => $data,
                    'headers' => $this->getHeaders(),
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
     * @return ResponseInterface
    */
    protected function postCall(string $path, array $data): ResponseInterface
    {
        try {
            $response = $this->client->post(
                $path,
                [
                    'json' => $data,
                    'headers' => $this->getHeaders(),
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
     * @return ResponseInterface
    */
    protected function deleteCall(string $path): ResponseInterface
    {
        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
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
