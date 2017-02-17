<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Principal extends BaseResource
{
    protected static $pathName = 'principal';
    protected static $client;
    protected static $token;

    function __construct(Client $client, TokenStorage $tokenstorage)
    {
    	$user = $tokenstorage->getToken()->getUser();
    	static::$client = $client;
    	static::$token = $user->getToken();
    }
}