<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Organization extends BaseResource
{
    protected static $pathName = 'organizations';
    protected static $client;
    protected static $token;

    function __construct(Client $client, TokenStorage $tokenstorage)
    {
    	$user = $tokenstorage->getToken()->getUser();
    	static::$client = $client;
    	static::$token = $user->getToken();
    }

    public function managersget(string $id) {
        $response = static::$client->get(
            'organizations/'.$id.'/'.'managers',
            array(
                'headers' => self::getHeaders(),
                )
            );
        return json_decode($response->getBody(), true);
    }
}