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

    /**
     * Create new Service
     *
     * @param string      $name
     * @param string|null $description
     * @param string|null $url
     * @param string      $entityid
     * @return array expanded organization
     */
    public function create(string $name, string $description = null, string $uri = null, string $entityid)
    {
        $serviceData = array();
        $serviceData['name'] = $name;
        if ($description) {
            $serviceData['description'] = $description;
        }
        if ($uri) {
            $serviceData['uri'] = $uri;
        }
        $serviceData['entityid'] = $entityid;
        $response = $this->post($serviceData);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $serviceId = preg_replace('#.*/#', '', $location);

        return $this->get($serviceId, "expanded");
    }

    /**
     * Create new permission
     *
     * @param string $id   of service
     * @param string $name
     * @param Entitlement $entitlement
     * @return ResponseInterface
     */
    public function createPermission(string $prefix, string $id, string $name, Entitlement $entitlement)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlements', array("uri" => $prefix.":".$id.":".$name, "name" => $name));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlement->get($id, "expanded");
    }

    /**
     * Create new permissionset
     *
     * @param string $id   of service
     * @param string $name
     * @param EntitlementPack $entitlementpack
     * @return ResponseInterface
     */
    public function createPermissionSet(string $id, string $name, EntitlementPack $entitlementpack)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlementpacks', array("name" => $name, "type" => "public"));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlementpack->get($id, "expanded");
    }

    /**
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putManager(string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/managers/'.$principalId, []);
    }
}
