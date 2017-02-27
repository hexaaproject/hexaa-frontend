<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Role extends BaseResource
{
    protected $pathName = 'roles';

    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }
}
