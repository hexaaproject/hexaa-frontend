<?php
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
}
