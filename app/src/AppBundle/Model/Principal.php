<?php
namespace AppBundle\Model;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Principal extends BaseResource
{
    protected $pathName = 'principal';

    public function getSelf(string $verbose = "normal") {
        return $this->getSingular($this->pathName.'/self', $verbose);
    }

    public function getAttributeValues(string $verbose = "normal", int $offset = 0, int $pageSize = 25) {
        return $this->getCollection($this->pathName.'/attributevalueprincipal', $verbose, $offset, $pageSize);
    }
}