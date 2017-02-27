<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Service extends BaseResource
{
    protected $pathName = 'services';

    public function managersget(string $id) {
        $response = $this->client->get(
            'services/'.$id.'/'.'managers',
            array(
                'headers' => self::getHeaders(),
                )
            );
        return json_decode($response->getBody(), true);
    }

    public function getAttributeSpecs(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/attributespecs', $verbose, $offset, $pageSize);
    }

    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $verbose, $offset, $pageSize);
    }

    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }

    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $verbose, $offset, $pageSize);
    }
}
