<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Organization extends BaseResource
{
    protected $pathName = 'organizations';

    public function managersget(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $verbose, $offset, $pageSize);
    }

    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $verbose, $offset, $pageSize);
    }

    public function getMembers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/members', $verbose, $offset, $pageSize);
    }

    public function getRoles(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/roles', $verbose, $offset, $pageSize);
    }

    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }

    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $verbose, $offset, $pageSize);
    }
}