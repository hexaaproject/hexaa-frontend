<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

abstract class BaseResource
{
    protected $pathName;
    /** @var  Client */
    protected $client;
    protected $token;


    function __construct(Client $client, TokenStorage $tokenStorage)
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


    public function getHeaders(): array
    {
        $config = $this->client->getConfig();
        $headers = $config["headers"];
        $headers['X-HEXAA-AUTH'] = $this->token;

        return $headers;
    }

    protected function getCollection(string $path, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        $response = $this->client->get(
          $path,
          array(
            'headers' => $this->getHeaders(),
            'query'   => array(
              'verbose' => $verbose,
              'offset'  => $offset,
              'limit'   => $pageSize,
            ),
          )
        );

        return json_decode($response->getBody(), true);
    }

    protected function getSingular(string $path, string $verbose = 'normal'): array
    {
        $response = $this->client->get(
          $path,
          array(
            'query'   => array('verbose' => $verbose),
            'headers' => $this->getHeaders(),
          )
        );

        return json_decode($response->getBody(), true);
    }

    protected function patchCall(string $path, array $data): ResponseInterface
    {
        $response = $this->client->put(
          $path,
          [
            'json'    => $data,
            'headers' => $this->getHeaders(),
          ]
        );

        if ($response->getStatusCode() !== 201 || $response->getStatusCode() !== 204) {
            throw new \Exception('Bad request'); // TODO: exception type, maybe chaining
        }

        return $response;
    }

    protected function putCall(string $path, array $data): ResponseInterface
    {
        $response = $this->client->put(
          $path,
          [
            'json'    => $data,
            'headers' => $this->getHeaders(),
          ]
        );

        if ($response->getStatusCode() !== 201 || $response->getStatusCode() !== 204) {
            throw new \Exception('Bad request'); // TODO: exception type, maybe chaining
        }

        return $response;
    }
}
