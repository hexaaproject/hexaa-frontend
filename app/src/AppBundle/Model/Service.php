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
     * GET all services
     *
     * @param string $admin    Admin call to get all services
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getAll(string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollectionAdmin(
            $this->pathName,
            $admin,
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
     * GET organizations link to Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getOrganizations(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/organizations',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET link requests to Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getLinkRequests(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/link'.'/requests',
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET link requests to Service
     *
     * @param string $id       ID of service
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getLinksOfService(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/link',
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
    public function create(string $name, string $description = null, string $url = null, string $entityid)
    {
        $serviceData = array();
        $serviceData['name'] = $name;
        if ($description) {
            $serviceData['description'] = $description;
        }
        if ($url) {
            $serviceData['url'] = $url;
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
     * @param string      $prefix
     * @param string      $id
     * @param string      $uriPost
     * @param string      $name
     * @param string      $description
     * @param Entitlement $entitlement
     * @return ResponseInterface
     */
    public function createPermission(string $prefix, string $id, string $uriPost, string $name, string $description = null, Entitlement $entitlement)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlements', array("uri" => $prefix.":".$id.":".$uriPost, "name" => $name, "description" => $description));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlement->get($id, "expanded");
    }


    /**
     * Create new permissionset
     *
     * @param string          $id
     * @param string          $name
     * @param EntitlementPack $entitlementpack
     * @return array
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
     * Create permission set in service page
     * @param string $id              of service
     * @param array  $entitlementPack
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function postPermissionSet(string $id, array $entitlementPack)
    {
        return $this->postCall($this->pathName.'/'.$id.'/entitlementpacks', $entitlementPack);
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

    /**
     * @param string $id
     * @param array  $contacts
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function notifySP(string $id, array $contacts)
    {
        return $this->putCall($this->pathName.'/'.$id.'/notifysp', array("contacts" => $contacts ));
    }

    /**
     * @param string $token
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function enableService(string $token)
    {
        return $this->putCall($this->pathName.'/'.$token.'/enable', []);
    }

    /**
     * Get the history of the service
     * @param string      $id
     * @param string      $verbose
     * @param int         $offset
     * @param int         $pageSize
     * @param string|null $tags
     * @return array
     */
    public function getHistory(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 500, string $tags = null)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/news', $verbose, $offset, $pageSize, $tags);
    }

    /**
     * @param string $path
     * @param string $admin
     * @param string $verbose
     * @param int  $offset
     * @param int  $pageSize
     * @return array
     */
    protected function getCollectionAdmin(string $path, string $admin = "true", string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        $response = $this->client->get(
            $path,
            array(
                'headers' => $this->getHeaders(),
                'query' => array(
                    'verbose' => $verbose,
                    'offset' => $offset,
                    'limit' => $pageSize,
                    'admin' => $admin,
                ),
            )
        );

        return json_decode($response->getBody(), true);
    }
}
