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
        return $this->getCollection(
            $this->pathName.'/'.$id.'/attributespecs',
            $verbose,
            $offset,
            $pageSize
        );
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
        return $this->getCollection(
            $this->pathName.'/'.$id.'/managers',
            $verbose,
            $offset,
            $pageSize
        );
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
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlements',
            $verbose,
            $offset,
            $pageSize
        );
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
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlementpacks',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     *DELETE managers of Service
     *
     * @param string $id  ID of service
     * @param string $pid ID of principal
     * @return response
     */
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

    /**
     *DELETE attribute specifications of Service
     *
     * @param string $id   ID of service
     * @param string $asid ID of attribute specification
     * @return response
     */
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

    /**
     *Add attribute specification to Service
     *
     * @param string $id       ID of service
     * @param string $asid     ID of attribute specification
     * @param bool   $ispublic Attribute specification is public or not
     * @return response
     */
    public function addAttributeSpec(string $id, string $asid, bool $ispublic = true)
    {

        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->putCall(
            $path,
            [
            'is_public' => $ispublic,
            ]
        );

        return $response;
    }
}
