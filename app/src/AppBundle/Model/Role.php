<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Role extends BaseResource
{
    protected static $pathName = 'roles';
    protected static $client;
    protected static $token;

    function __construct(Client $client, TokenStorage $tokenstorage)
    {
    	$user = $tokenstorage->getToken()->getUser();
    	static::$client = $client;
    	static::$token = $user->getToken();
    }
}
