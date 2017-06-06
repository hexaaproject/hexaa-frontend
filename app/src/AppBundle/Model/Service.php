<?php

namespace AppBundle\Model;

use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Service
 * @package AppBundle\Model
 */
class Service extends AbstractBaseResource
{
    protected $pathName = 'services';

    /**
     * GET attribute specifications of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAttributeSpecs(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/attributespecs',
                $verbose, $offset, $pageSize);
    }

    /**
     * GET managers of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/managers',
                $verbose, $offset, $pageSize);
    }

    /**
     * GET entitlements of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements',
                $verbose, $offset, $pageSize);
    }

    /**
     * GET entitlement packs of Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks',
                $verbose, $offset, $pageSize);
    }

    public function deleteMember(string $id, string $pid)
    {
        $path = $this->pathName.'/'.$id.'/managers/'.$pid;

        $response = $this->client->delete(
            $path,
            [
            'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }
    
     public function deleteAttributeSpec(string $id, string $asid)
    {
        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->client->delete(
            $path,
            [
            'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }

}
