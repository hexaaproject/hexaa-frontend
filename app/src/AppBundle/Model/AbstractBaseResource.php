<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
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

    /**
     * BaseResource constructor.
     * @param Client       $client
     * @param TokenStorage $tokenStorage
     */
    public function __construct(Client $client, TokenStorage $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $this->client = $client;
        $this->token = $user->getToken();
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
     * @return array
     */
    public function getHeaders(): array
    {
        $config = $this->client->getConfig();
        $headers = $config["headers"];
        $headers['X-HEXAA-AUTH'] = $this->token;

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
     * @return array
     */
    protected function getCollection(string $path, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
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
     */
    protected function patchCall(string $path, array $data): ResponseInterface
    {
        $response = $this->client->patch(
            $path,
            [
                'json' => $data,
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
    protected function putCall(string $path, array $data): ResponseInterface
    {
        $response = $this->client->put(
            $path,
            [
                'json' => $data,
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
    protected function postCall(string $path, array $data): ResponseInterface
    {
        $response = $this->client->post(
            $path,
            [
                'json' => $data,
                'headers' => $this->getHeaders(),
            ]
        );
        
        return $response->getHeader('Location');
    }
}
