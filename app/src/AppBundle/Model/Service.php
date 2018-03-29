<?php

namespace AppBundle\Model;

use AppBundle\Tools\Warning\DisabledServiceWarning;
use AppBundle\Tools\Warning\OrphanPermissionSetWarning;
use AppBundle\Tools\Warning\OrphanPermissionWarning;
use AppBundle\Tools\Warning\PendingLinkWarning;
use AppBundle\Tools\Warning\WarningableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Service
 * @package AppBundle\Model
 */
class Service extends AbstractBaseResource implements WarningableInterface
{
    protected $pathName = 'services';

    /**
     * GET attribute specifications of Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getAttributeSpecs(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/attributespecs',
            $hexaaAdmin,
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
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getManagers(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/managers',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET entitlements of Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlements',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET entitlement packs of Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/entitlementpacks',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET organizations link to Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getOrganizations(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/organizations',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET link requests to Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getLinkRequests(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/link'.'/requests',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }

    /**
     * GET link requests to Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getLinksOfService(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25)
    {
        return $this->getCollection(
            $this->pathName.'/'.$id.'/link',
            $hexaaAdmin,
            $verbose,
            $offset,
            $pageSize
        );
    }


    /**
     *DELETE managers of Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $pid        ID of principal
     * @return response
     */
    public function deleteMember(string $hexaaAdmin, string $id, string $pid)
    {
        $path = $this->pathName.'/'.$id.'/managers/'.$pid;

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                  'admin' => $hexaaAdmin,
                ),
            ]
        );

        return $response;
    }

    /**
     *DELETE attribute specifications of Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $asid       ID of attribute specification
     * @return response
     */
    public function deleteAttributeSpec(string $hexaaAdmin, string $id, string $asid)
    {
        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
                'query' => array(
                    'admin' => $hexaaAdmin,
                ),
            ]
        );

        return $response;
    }

    /**
     *Add attribute specification to Service
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of service
     * @param string $asid       ID of attribute specification
     * @param bool   $ispublic   Attribute specification is public or not
     * @return response
     */
    public function addAttributeSpec(string $hexaaAdmin, string $id, string $asid, bool $ispublic = true)
    {

        $path = $this->pathName.'/'.$id.'/attributespecs/'.$asid;

        $response = $this->putCall(
            $path,
            [
            'is_public' => $ispublic,
            ],
            $hexaaAdmin
        );

        return $response;
    }

    /**
     * Create new Service
     *
     * @param string      $hexaaAdmin  Admin hat
     * @param string      $name
     * @param string|null $description
     * @param string|null $url
     * @param string      $entityid
     * @return array expanded organization
     */
    public function create(string $hexaaAdmin, string $name, string $description = null, string $url = null, string $entityid = null)
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
        $response = $this->post($hexaaAdmin, $serviceData);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $serviceId = preg_replace('#.*/#', '', $location);

        return $this->get($hexaaAdmin, $serviceId, "expanded");
    }

    /**
     * Create new permission
     *
     * @param string      $hexaaAdmin  Admin hat
     * @param string      $prefix
     * @param string      $id
     * @param string      $uriPost
     * @param string      $name
     * @param string      $description
     * @param Entitlement $entitlement
     * @return ResponseInterface
     */
    public function createPermission(string $hexaaAdmin, string $prefix, string $id, string $uriPost, string $name, string $description = null, Entitlement $entitlement)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlements', array("uri" => $prefix.":".$id.":".$uriPost, "name" => $name, "description" => $description), $hexaaAdmin);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlement->get($hexaaAdmin, $id, "expanded");
    }


    /**
     * Create new permissionset
     *
     * @param string          $hexaaAdmin      Admin hat
     * @param string          $id
     * @param string          $name
     * @param EntitlementPack $entitlementpack
     * @return array
     */
    public function createPermissionSet(string $hexaaAdmin, string $id, string $name, EntitlementPack $entitlementpack)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlementpacks', array("name" => $name, "type" => "public"), $hexaaAdmin);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlementpack->get($hexaaAdmin, $id, "expanded");
    }

    /**
     * Create permission set in service page
     * @param string                           $hexaaAdmin      Admin hat
     * @param string                           $id              of service
     * @param array                            $entitlementPack
     * @param \AppBundle\Model\EntitlementPack $entitlementpack
     * @return array
     */
    public function postPermissionSet(string $hexaaAdmin, string $id, array $entitlementPack, EntitlementPack $entitlementpack)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/entitlementpacks', $entitlementPack, $hexaaAdmin);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $entitlementpack->get($hexaaAdmin, $id, "expanded");
    }

    /**
     * @param string $hexaaAdmin  Admin hat
     * @param string $id
     * @param string $principalId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putManager(string $hexaaAdmin, string $id, string $principalId)
    {
        return $this->putCall($this->pathName.'/'.$id.'/managers/'.$principalId, [], $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param array  $contacts
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function notifySP(string $hexaaAdmin, string $id, array $contacts)
    {
        return $this->putCall($this->pathName.'/'.$id.'/notifysp', array("contacts" => $contacts ), $hexaaAdmin);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $token
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function enableService(string $hexaaAdmin, string $token)
    {
        return $this->putCall($this->pathName.'/'.$token.'/enable', [], $hexaaAdmin);
    }

    /**
     * Get the history of the service
     * @param string      $hexaaAdmin Admin hat
     * @param string      $id
     * @param string      $verbose
     * @param int         $offset
     * @param int         $pageSize
     * @param string|null $tags
     * @return array
     */
    public function getHistory(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 500, string $tags = null)
    {
        return $this->getCollection($this->pathName.'/'.$id.'/news', $hexaaAdmin, $verbose, $offset, $pageSize, $tags);
    }

    /**
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param array  $resources
     *
     * @return ArrayCollection
     */
    public function getWarnings(string $hexaaAdmin, $id, array $resources)
    {
        $warnings = new ArrayCollection();

        $entitlements = $this->getEntitlements($hexaaAdmin, $id);
        $entitlementIds = array();
        foreach ($entitlements['items'] as $entitlement) {
            $entitlementIds[$entitlement['id']] = $entitlement;
        }

        $entitlementPacks = $this->getEntitlementPacks($hexaaAdmin, $id);
        $entitlementPackIds = array();
        foreach ($entitlementPacks['items'] as $entitlementPack) {
            $entitlementPackIds[$entitlementPack['id']] = $entitlementPack;
        }

        /** @var Link $linkResource */
        $linkResource = $resources['linkResource'];
        $links = $this->getLinksOfService($hexaaAdmin, $id);
        foreach ($links['items'] as $link) {
            if ('pending' == $link['status']) {
                $warnings->add(new PendingLinkWarning('Organization: '.$link['organization_id']));
            }

            $linkEntitlements = $linkResource->getEntitlements($hexaaAdmin, $link['id']);
            foreach ($linkEntitlements['items'] as $linkEntitlement) {
                if (array_key_exists($linkEntitlement['id'], $entitlementIds)) {
                    unset($entitlementIds[$linkEntitlement['id']]);
                }
            }

            $linkEntitlementPacks = $linkResource->getEntitlementPacks($hexaaAdmin, $link['id']);
            foreach ($linkEntitlementPacks['items'] as $entitlementPack) {
                if (array_key_exists($entitlementPack['id'], $entitlementPackIds)) {
                    unset($entitlementPackIds[$entitlementPack['id']]);
                }

                foreach ($entitlementPack['entitlement_ids'] as $entitlementId) {
                    if (array_key_exists($entitlementId, $entitlementIds)) {
                        unset($entitlementIds[$entitlementId]);
                    }
                }
            }
        }

        foreach ($entitlementIds as $entitlementId) {
            $warnings->add(new OrphanPermissionWarning($entitlementId['name']));
        }

        foreach ($entitlementPackIds as $entitlementPackId) {
            $warnings->add(new OrphanPermissionSetWarning($entitlementPackId['name']));
        }

        $service = $this->get($hexaaAdmin, $id);
        if (! $service['is_enabled']) {
            $warnings->add(new DisabledServiceWarning());
        }

        return $warnings;
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
