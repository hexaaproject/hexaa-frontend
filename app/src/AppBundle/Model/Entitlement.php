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
    
    public function getEntitlement(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id,
                $verbose, $offset, $pageSize);
    }
}
