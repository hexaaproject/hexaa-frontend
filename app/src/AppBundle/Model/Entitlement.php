<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Entitlement extends BaseResource
{
    protected $pathName = 'entitlements';
}