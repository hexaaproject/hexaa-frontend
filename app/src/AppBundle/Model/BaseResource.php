<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
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

    protected function getCollection(string $path, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
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


    public function cget(string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName, $verbose, $offset, $pageSize);
    }

    protected function getSingular(string $path, string $verbose = 'normal') {
        $response = $this->client->get(
          $path,
          array(
            'query'   => array('verbose' => $verbose),
            'headers' => $this->getHeaders(),
          )
        );

        return json_decode($response->getBody(), true);
    }

    public function get(string $id, string $verbose = "normal")
    {
        return $this->getSingular($this->pathName.'/'.$id, $verbose);
    }

    public function rget(string $id, string $verbose = "normal")
    {
        $response = $this->client->get(
          $this->pathName.'/'.$id.'/'.'roles',
          [
            'headers' => $this->getHeaders(),
            'query'   => [
              'verbose' => $verbose,
            ],
          ]
        );

        return json_decode($response->getBody(), true);
    }

    public function serviceattributesget(string $id)
    {
        $response = $this->client->get(
          $this->pathName.'/'.$id.'/'.'attributespecs',
          array(
            'headers' => $this->getHeaders(),
          )
        );

        return json_decode($response->getBody(), true);
    }

    public function attributespecsget(string $verbose = "normal")
    {
        $response = $this->client->get(
          'attributespecs',
          [
            'headers' => $this->getHeaders(),
            'query'   => [
              'verbose' => $verbose,
            ],
          ]
        );

        return json_decode($response->getBody(), true);
    }

    public function membersget(string $id)
    {
        $response = $this->client->get(
          $this->pathName.'/'.$id.'/'.'members',
          array(
            'headers' => $this->getHeaders(),
          )
        );

        return json_decode($response->getBody(), true);
    }

    public function principalinfo()
    {
        $response = $this->client->get(
          $this->pathName.'/'.'self',
          array(
            'headers' => $this->getHeaders(),
          )
        );

        return json_decode($response->getBody(), true);
    }

    public function attributeget(string $verbose = "normal")
    {
        $response = $this->client->get(
          $this->pathName.'/'.'attributevalueprincipal',
          [
            'headers' => $this->getHeaders(),
            'query'   => [
              'verbose' => $verbose,
            ],
          ]
        );

        return json_decode($response->getBody(), true);
    }

    public function entitlementsget(string $id, string $verbose = "normal")
    {
        $response = $this->client->get(
          $this->pathName.'/'.$id.'/'.'entitlements',
          [
            'headers' => $this->getHeaders(),
            'query'   => [
              'verbose' => $verbose,
            ],
          ]
        );

        return json_decode($response->getBody(), true);
    }

    public function entitlementpacksget(string $id, string $verbose = "normal")
    {
        $response = $this->client->get(
          $this->pathName.'/'.$id.'/'.'entitlementpacks',
          [
            'headers' => $this->getHeaders(),
            'query'   => [
              'verbose' => $verbose,
            ],
          ]
        );

        return json_decode($response->getBody(), true);
    }


    public function getHeaders()
    {
        $config = $this->client->getConfig();
        $headers = $config["headers"];
        $headers['X-HEXAA-AUTH'] = $this->token;

        return $headers;
    }
}
