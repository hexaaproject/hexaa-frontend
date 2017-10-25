<?php
namespace AppBundle\Model;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Organization
 * @package AppBundle\Model
 */
class Organization extends AbstractBaseResource
{
    protected $pathName = 'organizations';

    /**
     * GET managers of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getManagers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $verbose, $offset, $pageSize);
    }

    /**
     * GET members of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getMembers(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/members', $verbose, $offset, $pageSize);
    }

    /**
     * DELETE members of Organization
     *
     * @param string $id  ID of organization
     * @param string $pid Principal ID
     * @return ResponseInterface
     */
    public function deleteMember(string $id, string $pid)
    {
        $path = $this->pathName.'/'.$id.'/members/'.$pid;

        $response = $this->client->delete(
            $path,
            [
                'headers' => $this->getHeaders(),
            ]
        );

        return $response;
    }

    /**
     * DELETE manager of Organization
     *
     * @param string $id  ID of organization
     * @param string $pid Principal ID
     * @return ResponseInterface
     */
    public function deleteManager(string $id, string $pid)
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
     * GET roles of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getRoles(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/roles', $verbose, $offset, $pageSize);
    }


    /**
     * GET links of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getLinks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/link', $verbose, $offset, $pageSize);
    }

    /**
     * GET entitlements of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $verbose, $offset, $pageSize);
    }


    /**
     * GET entitlement packs of Organization
     *
     * @param string $id       ID of organization
     * @param string $verbose  One of minimal, normal or expanded
     * @param int    $offset   paging: item to start from
     * @param int    $pageSize paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $verbose, $offset, $pageSize);
    }

    /**
     * Create new Organization
     *
     * @param string      $name
     * @param string|null $description
     * @return array expanded organization
     */
    public function create(string $name, string $description = null)
    {
        $organizationData = array();
        $organizationData['name'] = $name;
        if ($description) {
            $organizationData['description'] = $description;
        }

        $response = $this->post($organizationData);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $organizationId = preg_replace('#.*/#', '', $location);

        return $this->get($organizationId, "expanded");
    }

    /**
     * Create new role
     *
     * @param string $id   of organization
     * @param string $name
     * @param Role   $role
     * @return ResponseInterface
     */
    public function createRole(string $id, string $name, Role $role)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/roles', array("name" => $name));
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $role->get($id, "expanded");
    }

    /**
     * Add manager to the organization
     *
     * @param string $id  of organization
     * @param string $pid of principal
     * @return ResponseInterface
     */
    public function addManager(string $id, string $pid)
    {
        return $this->putCall($this->pathName.'/'.$id.'/managers'.'/'.$pid, []);
    }

    /**
     * Link service to the organization
     *
     * @param string $id    of organization
     * @param string $token to link
     * @return ResponseInterface
     */
    public function connectService(string $id, string $token)
    {
        return $this->putCall($this->pathName.'/'.$id.'/links'.'/'.$token.'/token', []);
    }


    /**
     * Get the history of the organization
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
     * GET all organizations
     *
     * @param string $admin    Admin call to get all organizations
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
