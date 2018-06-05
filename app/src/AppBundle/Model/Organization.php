<?php
namespace AppBundle\Model;

use AppBundle\Tools\Warning\NoRolesWarning;
use AppBundle\Tools\Warning\RoleLessMemberWarning;
use AppBundle\Tools\Warning\WarningableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class Organization
 * @package AppBundle\Model
 */
class Organization extends AbstractBaseResource implements WarningableInterface
{
    protected $pathName = 'organizations';

    /**
     * GET managers of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getManagers(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/managers', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET members of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getMembers(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/members', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * DELETE members of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $pid        Principal ID
     * @return ResponseInterface
     */
    public function deleteMember(string $hexaaAdmin, string $id, string $pid)
    {
        $path = $this->pathName.'/'.$id.'/members/'.$pid;

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
     * DELETE manager of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $pid        Principal ID
     * @return ResponseInterface
     */
    public function deleteManager(string $hexaaAdmin, string $id, string $pid)
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
     * GET roles of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getRoles(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/roles', $hexaaAdmin, $verbose, $offset, $pageSize);
    }


    /**
     * GET attribute values of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getAttributevalues(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/attributevalueorganization', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET available attribute spec of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getAvailableAttributespecs(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/attributespecs', $hexaaAdmin, $verbose, $offset, $pageSize);
    }


    /**
     * GET organization attributes of attribute spec
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $asid       ID of attribute spec
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getAttributesOfAttributespecs(string $hexaaAdmin, string $id, string $asid, string $verbose = "normal", int $offset = 0, int $pageSize = 1000): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/attributespecs/'.$asid.'/attributevalueorganizations', $hexaaAdmin, $verbose, $offset, $pageSize);
    }



  /**
     * GET links of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getLinks(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 1000): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/link', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * GET entitlements of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlements(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $hexaaAdmin, $verbose, $offset, $pageSize);
    }


    /**
     * GET entitlement packs of Organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         ID of organization
     * @param string $verbose    One of minimal, normal or expanded
     * @param int    $offset     paging: item to start from
     * @param int    $pageSize   paging: number of items to return
     * @return array
     */
    public function getEntitlementPacks(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 25): array
    {
        return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $hexaaAdmin, $verbose, $offset, $pageSize);
    }

    /**
     * Create new Organization
     *
     * @param string      $hexaaAdmin  Admin hat
     * @param string      $name
     * @param string|null $description
     * @return array expanded organization
     */
    public function create(string $hexaaAdmin, string $name, string $description = null)
    {
        $organizationData = array();
        $organizationData['name'] = $name;
        if ($description) {
            $organizationData['description'] = $description;
        }

        $response = $this->post($this->hexaaAdmin, $organizationData);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $organizationId = preg_replace('#.*/#', '', $location);

        return $this->get($hexaaAdmin, $organizationId, "expanded");
    }

    /**
     * Create new role
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         of organization
     * @param string $name
     * @param Role   $role
     * @return ResponseInterface
     */
    public function createRole(string $hexaaAdmin, string $id, string $name, Role $role)
    {
        $response = $this->postCall($this->pathName.'/'.$id.'/roles', array("name" => $name), $hexaaAdmin);
        $locations = $response->getHeader('Location');
        $location = $locations[0];
        $id = preg_replace('#.*/#', '', $location);

        return $role->get($hexaaAdmin, $id, "expanded");
    }

    /**
     * Add manager to the organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         of organization
     * @param string $pid        of principal
     * @return ResponseInterface
     */
    public function addManager(string $hexaaAdmin, string $id, string $pid)
    {
        return $this->putCall($this->pathName.'/'.$id.'/managers'.'/'.$pid, [], $hexaaAdmin);
    }

    /**
     * Link service to the organization
     *
     * @param string $hexaaAdmin Admin hat
     * @param string $id         of organization
     * @param string $token      to link
     * @return ResponseInterface
     */
    public function connectService(string $hexaaAdmin, string $id, string $token)
    {
        return $this->putCall($this->pathName.'/'.$id.'/links'.'/'.$token.'/token', [], $hexaaAdmin);
    }


    /**
     * Get the history of the organization
     * @param string      $hexaaAdmin Admin hat
     * @param string      $id
     * @param string      $verbose
     * @param int         $offset
     * @param int         $pageSize
     * @param string|null $tags
     * @return array
     */
    public function getHistory(string $hexaaAdmin, string $id, string $verbose = "normal", int $offset = 0, int $pageSize = 500, array $tags = [])
    {
        dump($hexaaAdmin);
        return $this->getCollection($this->pathName.'/'.$id.'/news', $hexaaAdmin, $verbose, $offset, $pageSize, $tags);
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
     * @param string $hexaaAdmin Admin hat
     * @param string $id
     * @param array  $resources
     *
     * @return ArrayCollection
     */
    public function getWarnings(string $hexaaAdmin, $id, array $resources)
    {
        $roleResource = $resources["roleResource"];
        $warnings = new ArrayCollection();

        $roles = $this->getRoles($hexaaAdmin, $id);
        $members = $this->getMembers($hexaaAdmin, $id);
        $memberIds = array();
        foreach ($members['items'] as $member) {
            $memberIds[$member['id']] = $member;
        }
        if (0 == $roles['item_number']) {
            $warnings->add(new NoRolesWarning());
        }

        foreach ($roles['items'] as $role) {
            foreach ($roleResource->getWarnings($hexaaAdmin, $role['id']) as $warning) {
                $warnings->add($warning);
            };

            $principals = $roleResource->getPrincipals($hexaaAdmin, $id);
            if ($principals['item_number']) {
                foreach ($principals['items'] as $principal) {
                    $principalId = $principal['principal_id'];
                    if (array_key_exists($principalId, $memberIds)) {
                        unset($memberIds[$principalId]);
                    }
                }
            }
        }


        foreach ($memberIds as $memberId) {
            $warnings->add(new RoleLessMemberWarning($memberId['display_name']." &lt;".$memberId['fedid']."&gt;"));
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
